<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\components\helpers\Resource;
use app\models\IkalogVersion;
use app\models\WinikalogVersion;

class IkalogController extends Controller
{
    private $ikalogRepoPath;

    public function init()
    {
        $this->ikalogRepoPath = Yii::getAlias('@app/runtime/ikalog/repo');
    }

    public function actionUpdateIkalog()
    {
        $ikalogInfoList = $this->getIkalogRepoHistory();
        echo "Detected " . count($ikalogInfoList) . " commits.\n";
        usort($ikalogInfoList, function ($a, $b) {
            return $a->at - $b->at;
        });

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        // 検出したデータを投入するテンポラリテーブル
        echo "Creating temporary table and its data...\n";
        $db->createCommand(sprintf(
            'CREATE TEMPORARY TABLE {{tmp_ikalog_version}} ( %s )',
            implode(', ', [
                '[[revision]] CHAR(' . strlen(hash('sha1', '')) . ') NOT NULL UNIQUE',
                '[[summary]] TEXT NULL',
                '[[at]] TIMESTAMP(0) WITH TIME ZONE NOT NULL',
            ])
        ))->execute();
        $db->createCommand()->batchInsert(
            'tmp_ikalog_version',
            [ 'revision', 'summary', 'at' ],
            array_map(
                function ($o) {
                    return [
                        $o->revision,
                        $o->comment == '' ? null : $o->comment,
                        date('Y-m-d H:i:sP', $o->at),
                    ];
                },
                $ikalogInfoList
            )
        )->execute();

        echo "Registering new revisions...\n";
        $select = (new \yii\db\Query())
            ->select([
                'revision'  => '{{tmp}}.[[revision]]',
                'summary'   => '{{tmp}}.[[summary]]',
                'at'        => '{{tmp}}.[[at]]',
            ])
            ->from('tmp_ikalog_version tmp')
            ->leftJoin('ikalog_version o', '{{tmp}}.[[revision]] = {{o}}.[[revision]]')
            ->where(['{{o}}.[[id]]' => null])
            ->createCommand()
            ->rawSql;

        $sql = 'INSERT INTO ikalog_version ( revision, summary, at ) ' . $select;
        $count = $db->createCommand($sql)->execute();

        if ($count > 0) {
            echo "Registered {$count} commits.\n";
        } else {
            echo "No commits registered. (success)\n";
        }
        $transaction->commit();
    }

    private function getIkalogRepoHistory()
    {
        $ret = [];
        foreach ($this->getIkaLogRepoBranches() as $branch) {
            $ret = array_merge($ret, $this->getIkalogRepoHistoryImpl($branch));
        }
        return $ret;
    }

    private function getIkalogRepoHistoryImpl($branch)
    {
        $workDirState = static::pushd($this->ikalogRepoPath);
        echo "Getting commit history of {$branch} from ikalog repository ...\n";
        $cmdline = sprintf(
            'git log --pretty=%s --date=%s %s --',
            escapeshellarg('format:%H/%ad/%s'),
            escapeshellarg('iso'),
            escapeshellarg($branch)
        );
        $lines = $state = null;
        exec($cmdline, $lines, $state);
        if ($state !== 0) {
            throw new \Exception("exec() failed: state={$state}, cmdline={$cmdline}");
        }
        $ret = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('!^[[:xdigit:]]{40}/!', $line)) {
                list($revision, $at, $comment) = explode('/', $line, 3);
                $at = strtotime($at);
                $comment = trim($comment);
                $ret[$revision] = (object)[
                    'revision'  => $revision,
                    'comment'   => $comment,
                    'at'        => $at,
                ];
            }
        }
        return $ret;
    }

    private function getIkaLogRepoBranches($origin = 'origin')
    {
        $workDirState = static::pushd($this->ikalogRepoPath);
        $cmdline = 'git branch -r';
        $lines = $state = null;
        exec($cmdline, $lines, $state);
        if ($state !== 0) {
            throw new \Exception("exec() failed: state={$state}, cmdline={$cmdline}");
        }
        $ret = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('!^' . preg_quote($origin, '!') . '/[\S]+$!', $line)) {
                $ret[] = $line;
            }
        }
        return $ret;
    }

    public function actionUpdateWinikalog()
    {
        $htmlPath = Yii::getAlias('@app/runtime/ikalog/winikalog.html');
        $list = $this->parseWinikalogHtml($htmlPath);
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($list as $winInfo) {
            $ikalog = IkalogVersion::findOneByRevision($winInfo->revision);
            if (!$ikalog) {
                continue;
            }
            if (!$winikalog = WinikalogVersion::findOne(['revision_id' => $ikalog->id])) {
                echo "New WinIkaLog " . $winInfo->revision . " found.\n";
                $winikalog = new WinikalogVersion();
                $winikalog->revision_id = $ikalog->id;
                $winikalog->build_at = date('Y-m-d H:i:sO', $winInfo->at);
                $winikalog->save();
            }
        }
        $transaction->commit();
    }

    private function parseWinikalogHtml($htmlPath)
    {
        $html = file_get_contents($htmlPath);
        preg_match_all('/\bWin(?:Ika|Tako)Log(\d{8})_(\d{6})_([0-9a-f]{7,})/', $html, $matches, PREG_SET_ORDER);
        return array_reverse(
            array_map(
                function ($match) {
                    return (object)[
                        'revision' => $match[3],
                        'at' => strtotime($match[1] . 'T' . $match[2] . '+09:00'),
                    ];
                },
                $matches
            )
        );
    }

    private static function pushd($dir)
    {
        $old = getcwd();
        chdir($dir);
        return new Resource($old, 'chdir');
    }
}
