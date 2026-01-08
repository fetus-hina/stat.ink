<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Yii;
use app\models\UserAuthKey;
use app\models\UserLoginHistory;
use yii\console\Controller;
use yii\console\ExitCode;

final class CleanupController extends Controller
{
    /**
     * @var string
     */
    public $defaultAction = 'cleanup';

    public function actionCleanup(): int
    {
        $tasks = [
            fn (): int => $this->actionLoginHistory(),
            fn (): int => $this->actionUserAuthKey(),
        ];

        $exitCode = ExitCode::OK;
        foreach ($tasks as $task) {
            $tmpCode = $task();
            if ($exitCode === ExitCode::OK && $tmpCode !== ExitCode::OK) {
                $exitCode = $tmpCode;
            }
        }

        return $exitCode;
    }

    public function actionLoginHistory(): int
    {
        $time = (new DateTimeImmutable())
            ->setTimestamp($_SERVER['REQUEST_TIME'])
            ->sub(new DateInterval('P30D'));

        $this->stdout("Delete login history before {$time->format(DateTime::ATOM)}\n");
        UserLoginHistory::deleteAll(['and',
            ['<', 'created_at', $time->format(DateTime::ATOM)],
        ]);

        $this->vacuumTables([
            UserLoginHistory::tableName(),
        ]);

        return ExitCode::OK;
    }

    public function actionUserAuthKey(): int
    {
        $time = (new DateTimeImmutable())
            ->setTimestamp($_SERVER['REQUEST_TIME'])
            ->sub(new DateInterval('P1D'));

        $this->stdout("Delete user auth key expired before {$time->format(DateTime::ATOM)}\n");
        UserAuthKey::deleteAll(['and',
            ['<', 'expires_at', $time->format(DateTime::ATOM)],
        ]);

        $this->vacuumTables([
            UserAuthKey::tableName(),
        ]);

        return ExitCode::OK;
    }

    private function vacuumTables(array $tableNames): void
    {
        $db = Yii::$app->db;
        $this->stdout("Vacuuming tables...\n");
        foreach ($tableNames as $table) {
            $this->stdout("  {$table}...");
            $db->createCommand("VACUUM (ANALYZE) {$table}")->execute();
            $this->stdout(" done.\n");
        }
        $this->stdout("done.\n");
    }
}
