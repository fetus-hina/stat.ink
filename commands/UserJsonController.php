<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use Exception;
use Yii;
use app\models\Battle;
use app\models\User;
use yii\base\InlineAction;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\FileHelper;
use yii\helpers\Json;

use function clearstatcache;
use function count;
use function date;
use function dirname;
use function escapeshellarg;
use function exec;
use function fclose;
use function fgets;
use function file_exists;
use function file_get_contents;
use function filemtime;
use function filesize;
use function flock;
use function fopen;
use function fseek;
use function ftell;
use function ftruncate;
use function fwrite;
use function gzencode;
use function memory_get_usage;
use function number_format;
use function passthru;
use function printf;
use function random_int;
use function rtrim;
use function sprintf;
use function strtotime;
use function touch;
use function unlink;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const LOCK_EX;
use const LOCK_NB;
use const LOCK_UN;
use const SEEK_END;
use const SEEK_SET;

class UserJsonController extends Controller
{
    public $defaultAction = 'auto-update';

    /**
     * @var string JSONの保存先のパス
     */
    public $basePath;

    /**
     * @var int 対象にするユーザのID
     */
    public $userId;

    /**
     * @var string 対象にするユーザのscreen name
     */
    public $screenName;

    public function init()
    {
        parent::init();
        if ($this->basePath === null) {
            $this->basePath = Yii::getAlias('@app/runtime/user-json');
        }
        Yii::setAlias('@web', rtrim(Yii::$app->urlManager->baseUrl, '/'));
    }

    public function options($actionId)
    {
        $ret = parent::options($actionId);
        if ($actionId === 'update') {
            $ret[] = 'userId';
            $ret[] = 'screenName';
            $ret[] = 'basePath';
        } elseif ($actionId === 'auto-update') {
            $ret[] = 'basePath';
        }
        return $ret;
    }

    public function getActionOptionsHelp($action)
    {
        if ($action instanceof InlineAction) {
            return parent::getActionOptionsHelp($action);
        }

        return [];
    }

    public function actionAutoUpdate()
    {
        $lockPath = $this->basePath . '/.auto-update';
        if (!file_exists(dirname($lockPath))) {
            FileHelper::createDirectory(dirname($lockPath));
        }
        if (!$lock = fopen($lockPath, 'c+')) {
            echo "Could not open lock file: $lockPath\n";
            return 1;
        }
        if (!flock($lock, LOCK_EX | LOCK_NB)) {
            echo "Could not get file lock. Another process running?\n";
            return 1;
        }
        $lastExec = (int)fgets($lock);

        $query = (new Query())
            ->select([
                'user_id',
                'last_updated' => 'MAX({{battle}}.[[at]])',
            ])
            ->from('battle')
            ->where(['>=', '{{battle}}.[[at]]', date('Y-m-d\TH:i:sP', $lastExec)])
            ->groupBy('{{battle}}.[[user_id]]')
            ->orderBy('MAX({{battle}}.[[at]]), {{battle}}.[[user_id]]');
        echo $query->createCommand()->rawSql . "\n";

        foreach ($query->each() as $row) {
            $jsonPath = $this->basePath . '/' . $row['user_id'] . '.json.gz';
            if (!file_exists($jsonPath) || filemtime($jsonPath) < strtotime($row['last_updated'])) {
                $cmdline = sprintf(
                    '/usr/bin/env %s user-json/update --userId=%s --basePath=%s',
                    escapeshellarg(dirname(__DIR__) . '/yii'),
                    escapeshellarg($row['user_id']),
                    escapeshellarg($this->basePath),
                );
                passthru($cmdline);
            } else {
                echo "Skip {$row['user_id']}\n";
            }
            clearstatcache($jsonPath);
        }

        fseek($lock, 0, SEEK_SET);
        fwrite($lock, $_SERVER['REQUEST_TIME'] . "\n");
        ftruncate($lock, ftell($lock));
        flock($lock, LOCK_UN);
        fclose($lock);
    }

    public function actionUpdate()
    {
        if (!$this->findUser()) {
            $this->stdErr("ユーザが見つかりません (userId か screenName のどちらかは必須です)\n");
            return 1;
        }

        $lastId = $this->getLastSavedBattleId();
        $count = 0;
        while (true) {
            printf("<Memory: %s>\n", number_format(memory_get_usage(true)));
            if (!$list = $this->getNextBattles($lastId)) {
                break;
            }
            echo "Converting...\n";
            foreach ($list as $battle) {
                $this->appendJson(
                    Json::encode($battle->toJsonArray(['user']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    strtotime($battle->at),
                );
                $lastId = $battle->id;
                ++$count;
            }
        }
        if ($count && random_int(0, 29) === 0) {
            $this->recompress();
        }
    }

    public function getJsonPath()
    {
        return $this->basePath . '/' . $this->findUser()->id . '.json.gz';
    }

    protected $user = false;

    protected function findUser()
    {
        if ($this->user === false) {
            $where = ['and'];
            if ($this->userId) {
                $where[] = ['{{user}}.[[id]]' => $this->userId];
            }
            if ($this->screenName) {
                $where[] = ['{{user}}.[[screen_name]]' => $this->screenName];
            }
            if (count($where) === 1) {
                $where[] = '0 = 1';
            }
            $this->user = User::find()->andWhere($where)->limit(1)->one();
        }
        return $this->user;
    }

    public function getLastSavedBattleId(): string
    {
        $jsonPath = $this->jsonPath;
        if (!file_exists($jsonPath)) {
            return '0';
        }
        $last = Battle::find()
            ->andWhere(['and',
                ['{{battle}}.[[user_id]]' => $this->findUser()->id],
                ['<=', '{{battle}}.[[at]]', date('Y-m-d\TH:i:sP', filemtime($jsonPath))],
            ])
            ->orderBy('{{battle}}.[[id]] DESC')
            ->limit(1)
            ->one();
        return (string)($last->id ?? 0);
    }

    protected function getNextBattles(string $lastBattleId): array
    {
        $query = Battle::find()
            ->innerJoinWith('user')
            ->with([
                'agent',
                'agentGameVersion',
                'battleDeathReasons',
                'battleEvents',
                'battleImageGear',
                'battleImageJudge',
                'battleImageResult',
                'battlePlayers',
                'battlePlayers.rank',
                'battlePlayers.weapon',
                'bonus',
                'env',
                'festTitle',
                'festTitleAfter',
                'gender',
                'lobby',
                'map',
                'rank',
                'rank.group',
                'rankAfter',
                'rankAfter.group',
                'rule',
                'rule.mode',
                'splatoonVersion',
                'user.env',
                'user.userStat',
                'weapon',
                'weapon.special',
                'weapon.subweapon',
                'weapon.type',
            ])
            ->andWhere(['and',
                ['{{battle}}.[[user_id]]' => $this->findUser()->id],
                ['>', '{{battle}}.[[id]]', $lastBattleId],
            ])
            ->orderBy('{{battle}}.[[id]] ASC')
            ->limit(10);
        foreach (['headgear', 'clothing', 'shoes'] as $gearKey) {
            $query->with([
                "{$gearKey}",
                "{$gearKey}.primaryAbility",
                "{$gearKey}.gear",
                "{$gearKey}.gear.brand",
                "{$gearKey}.gear.brand.strength",
                "{$gearKey}.gear.brand.weakness",
                "{$gearKey}.secondaries",
                "{$gearKey}.secondaries.ability",
            ]);
        }
        echo $query->createCommand()->rawSql . "\n";
        return $query->all();
    }

    protected function appendJson($text, $mtime)
    {
        $path = $this->jsonPath;
        if (!file_exists($path)) {
            if (!file_exists(dirname($path))) {
                FileHelper::createDirectory(dirname($path));
            }
        }

        if (!$fh = @fopen($path, 'cb')) {
            throw new Exception('Could not open file in "c" mode: ' . $path);
        }
        flock($fh, LOCK_EX);
        fseek($fh, 0, SEEK_END);
        fwrite($fh, gzencode($text . "\n", 3));
        flock($fh, LOCK_UN);
        fclose($fh);
        touch($path, $mtime);
    }

    protected function recompress()
    {
        $mainPath = $this->jsonPath;
        $tmpPath = $mainPath . '.tmp';
        if (!file_exists($mainPath)) {
            throw new Exception('File does not exists : ' . $mainPath);
        }
        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }

        // 書き換えを防ぐためにロックする
        if (!$fh = @fopen($mainPath, 'r+')) {
            throw new Exception('Could not open file in "r+" mode: ' . $mainPath);
        }
        flock($fh, LOCK_EX);
        $mtime = filemtime($mainPath);

        // 再圧縮処理
        echo "Recompressing...\n";
        $cmdline = sprintf(
            '/usr/bin/zcat %s | /usr/bin/gzip -9c > %s',
            $mainPath,
            $tmpPath,
        );
        exec($cmdline, $lines, $status);
        if ($status !== 0) {
            @unlink($tmpPath);
            flock($fh, LOCK_UN);
            fclose($fh);
            throw new Exception('Could not recompress file');
        }
        echo "Recomressed.\n";
        echo '  Before: ' . number_format(filesize($mainPath)) . "\n";
        echo '  After:  ' . number_format(filesize($tmpPath)) . "\n";

        if (filesize($mainPath) > filesize($tmpPath)) {
            echo "Writing...\n";
            fseek($fh, 0, SEEK_SET);
            fwrite($fh, file_get_contents($tmpPath));
            ftruncate($fh, ftell($fh));
        }

        flock($fh, LOCK_UN);
        fclose($fh);
        touch($mainPath, $mtime);

        unlink($tmpPath);
    }
}
