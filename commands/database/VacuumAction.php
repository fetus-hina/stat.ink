<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\database;

use Yii;
use yii\base\Action;
use yii\console\ExitCode;
use yii\db\Connection;

use function assert;
use function microtime;
use function preg_match;
use function vfprintf;

use const STDERR;

final class VacuumAction extends Action
{
    public function run(): int
    {
        $db = Yii::$app->db;
        assert($db instanceof Connection);

        vfprintf(STDERR, "Vacuuming database (database=%s)\n", [
            $this->getDatabaseName($db) ?? '[unknown]',
        ]);

        $t1 = microtime(true);
        $db->createCommand('VACUUM ( ANALYZE )')->execute();
        $t2 = microtime(true);

        vfprintf(STDERR, "Done the vacuum, took=%.3fsec\n", [
            $t2 - $t1,
        ]);

        return ExitCode::OK;
    }

    private function getDatabaseName(Connection $db): ?string
    {
        return preg_match('/^.*?dbname=([\w-]+)\b.*$/', $db->dsn, $match)
            ? $match[1]
            : null;
    }
}
