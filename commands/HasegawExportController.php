<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use PDO;
use Yii;
use app\components\helpers\Resource;
use app\models\Battle;
use app\models\BattlePlayer;
use yii\console\Controller;
use yii\helpers\Json;

class HasegawExportController extends Controller
{
    public const EXPORT_CSV_VERSION = '1';

    public $defaultAction = 'csv';
    public $outputDirectory = '@app/runtime/hasegaw-export';

    public function init()
    {
        parent::init();
        Yii::$app->timeZone = 'Asia/Tokyo';
    }

    public function actionCsvAll()
    {
        $now = new DateTimeImmutable('now', new DateTimeZone(Yii::$app->timeZone));
        $query = Battle::find()
            ->asArray()
            ->innerJoinWith('agent', false)
            ->innerJoinWith('user', false)
            ->andWhere(['and',
                ['{{agent}}.[[name]]' => [ 'IkaLog', 'TakoLog' ]],
                ['<', '{{battle}}.[[at]]', $now->format('Y-m-d\T00:00:00O')],
            ])
            ->select([
                'min' => 'MIN({{battle}}.[[at]])',
                'max' => 'MAX({{battle}}.[[at]])',
            ])
            ->orderBy(null);
        $info = $query->one();

        $from = (new DateTimeImmutable($info['min']))
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
            ->setTime(0, 0, 0);
        $to = (new DateTimeImmutable($info['max']))
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
            ->setTime(23, 59, 59);
        for ($date = $from; $date <= $to; $date = $date->add(new DateInterval('P1D'))) {
            $this->actionCsv($date->format('Y-m-d'), false);
        }
    }

    public function actionCsv($date = null, $overwrite = true)
    {
        $date = $this->parseDate($date);
        $outputFileName = sprintf(
            '%s/csv-v%d/%s.csv.xz',
            Yii::getAlias($this->outputDirectory),
            static::EXPORT_CSV_VERSION,
            $date->format('Y-m-d')
        );
        if (!file_exists(dirname($outputFileName))) {
            mkdir(dirname($outputFileName), 0755, true);
        }
        if (!$overwrite && file_exists($outputFileName)) {
            printf("%s: Already exists. skip.\n", basename($outputFileName));
            return 0;
        }
        printf("Exporting to %s\n", $outputFileName);
        $cmdline = sprintf(
            '/usr/bin/env %s -zc9eq --threads=0 - > %s',
            escapeshellarg('xz'),
            escapeshellarg($outputFileName)
        );
        $fh = new Resource(popen($cmdline, 'w'), 'pclose');
        $list = $this->queryExport($date);
        $count = 0;
        foreach ($list as $row) {
            fwrite($fh->get(), $this->formatCsvRow($row) . "\n");
            ++$count;
        }
        echo "done. {$count}\n";
    }

    public function actionBattleDb()
    {
        $outputFileName = sprintf(
            '%s/battles/battles.db',
            Yii::getAlias($this->outputDirectory)
        );
        if (!file_exists(dirname($outputFileName))) {
            mkdir(dirname($outputFileName), 0755, true);
        }
        if (!file_exists($outputFileName)) {
            $this->initBattleDb($outputFileName);
        }
        $exp = $this->connectSqlite($outputFileName);
        $quote = function ($v) use ($exp) {
            if ($v === null) {
                return 'null';
            }
            return $exp->quote($v);
        };
        $startId = $exp->query('SELECT MAX("id") FROM "battle"')->fetchColumn(0) + 1;
        echo "Export start from {$startId}\n";
        while (true) {
            echo "{$startId} - \n";
            $exp->beginTransaction();
            $insert = [];
            $battleIdList = [];
            foreach ($this->queryBattleDbExport($startId) as $battle) {
                $insert[] = sprintf(
                    '(%d,%s,%s)',
                    $battle['id'],
                    $exp->quote($battle['user']),
                    $exp->quote($battle['agent_version'])
                );
                $battleIdList[] = $battle['id'];
                $startId = $battle['id'] + 1;
            }
            if (!$insert) {
                break;
            }
            $exp->exec('INSERT INTO "battle"("id","user","agent_version") VALUES ' . implode(',', $insert));

            $insert = [];
            $query = BattlePlayer::find()
                ->asArray()
                ->with('weapon')
                ->with('rank')
                ->andWhere(['battle_id' => $battleIdList])
                ->orderBy('id ASC');
            foreach ($query->each() as $row) {
                $insert[] = implode(',', [
                    (int)$row['id'],
                    (int)$row['battle_id'],
                    $row['is_me'] ? 1 : 0,
                    $row['is_my_team'] ? 1 : 0,
                    (int)$row['rank_in_team'],
                    $quote($row['level']),
                    $quote($row['rank']['key'] ?? null),
                    $quote($row['weapon']['key'] ?? null),
                    $quote($row['kill']),
                    $quote($row['death']),
                    $quote($row['point']),
                ]);
            }
            if ($insert) {
                echo '  player: ' . count($insert) . "\n";
                $a = $exp->exec(
                    $sql = 'INSERT INTO "battle_player" ("' . implode('", "', [
                        'id', 'battle_id', 'is_me', 'is_my_team', 'rank_in_team',
                        'level', 'rank', 'weapon', 'kill', 'death', 'point',
                    ]) . '") VALUES (' . implode('), (', $insert) . ')'
                );
                if (!$a) {
                    echo Json::encode($exp->errorInfo());
                    exit;
                }
            }
            $exp->commit();
        }
    }

    private function parseDate($date): DateTimeInterface
    {
        $date = trim($date);
        if ($date === '') {
            $t = (int)($_SERVER['REQUEST_TIME'] ?? time());
            $date = date(
                'Y-m-d\TH:i:sO',
                mktime(0, 0, 0, date('n', $t), date('j', $t) - 1, date('Y', $t))
            );
        } elseif (preg_match('/^(\d{4})-?(\d{2})-?(\d{2})$/', $date, $match)) {
            $date = date(
                'Y-m-d\TH:i:sO',
                mktime(0, 0, 0, $match[2], $match[3], $match[1])
            );
        } else {
            throw new Exception('Could not parse date-string: ' . $date);
        }
        return (new DateTimeImmutable($date))
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
            ->setTime(0, 0, 0);
    }

    protected function queryExport(DateTimeInterface $date)
    {
        $query = Battle::find()
            ->asArray()
            ->innerJoinWith('agent', true)
            ->innerJoinWith('user', true)
            ->andWhere(['and',
                ['between', '{{battle}}.[[at]]', $date->format('Y-m-d\T00:00:00O'), $date->format('Y-m-d\T23:59:59O')],
                ['{{agent}}.[[name]]' => [ 'IkaLog', 'TakoLog' ]],
            ])
            ->orderBy('{{battle}}.[[id]] ASC');
        foreach ($query->each(200) as $row) {
            yield [
                (string)(int)$row['id'],
                $row['user']['screen_name'],
                $row['agent']['version'],
            ];
        }
    }

    protected function queryBattleDbExport($startId)
    {
        $query = Battle::find()
            ->asArray()
            ->innerJoinWith('agent', true)
            ->innerJoinWith('user', true)
            ->andWhere(['and',
                ['{{agent}}.[[name]]' => [ 'IkaLog', 'TakoLog' ]],
                ['>=', '{{battle}}.[[id]]', $startId],
            ])
            ->orderBy('{{battle}}.[[id]] ASC')
            ->limit(60);
        foreach ($query->all() as $row) {
            yield [
                'id' => $row['id'],
                'user' => $row['user']['screen_name'],
                'agent_version' => $row['agent']['version'],
            ];
        }
    }

    protected function formatCsvRow(array $row)
    {
        $ret = array_map(
            function ($cell) {
                $cell = mb_convert_encoding((string)$cell, 'UTF-8', 'UTF-8');
                if (!preg_match('/[",\x0d\x0a]/', $cell)) {
                    return $cell;
                }
                return '"' . mb_str_replace('"', '""', $cell, 'UTF-8') . '"';
            },
            $row
        );
        return implode(',', $ret);
    }

    protected function connectSqlite($path)
    {
        $dsn = 'sqlite:' . $path;
        return new PDO($dsn);
    }

    protected function initBattleDb($path)
    {
        $db = $this->connectSqlite($path);
        echo "Creating table: battle\n";
        $db->exec(<<<'SQL'
CREATE TABLE "battle" (
    "id" INTEGER NOT NULL PRIMARY KEY,
    "user" TEXT NOT NULL,
    "agent_version" TEXT NOT NULL
);
SQL
        );
        echo "Creating table: battle_player\n";
        $db->exec(<<<'SQL'
CREATE TABLE "battle_player" (
    "id" INTEGER NOT NULL PRIMARY KEY,
    "battle_id" INTEGER NOT NULL,
    "is_me" INTEGER NOT NULL,
    "is_my_team" INTEGER NOT NULL,
    "rank_in_team" INTEGER NOT NULL,
    "level" INTEGER,
    "rank" TEXT,
    "weapon" TEXT,
    "kill" INTEGER,
    "death" INTEGER,
    "point" INTEGER
);
SQL
        );
        echo "Creating index\n";
        $db->exec('CRETE INDEX ix_battle_player_battle ON battle_player ( battle_id )');
        echo "initialized\n";
    }
}
