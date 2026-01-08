<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Yii;
use yii\caching\DbCache;
use yii\caching\FileCache;
use yii\console\ExitCode;
use yii\console\controllers\CacheController as BaseController;
use yii\db\Connection;

use function array_keys;
use function func_get_args;
use function fwrite;
use function in_array;
use function sprintf;

use const STDERR;

final class CacheController extends BaseController
{
    public function actionGc(): int
    {
        $cachesInput = func_get_args();
        $caches = $this->findCaches($cachesInput);
        if (!$caches) {
            return ExitCode::OK;
        }

        $gcedTables = [];
        foreach (array_keys($caches) as $name) {
            $component = Yii::$app->get($name);
            if ($component) {
                if ($component instanceof FileCache) {
                    fwrite(STDERR, "GC'ing $name...\n");
                    $component->gc(force: true, expiredOnly: true);
                } elseif ($component instanceof DbCache) {
                    $tableName = $component->cacheTable;
                    if (in_array($tableName, $gcedTables, true)) {
                        fwrite(STDERR, "Skip GC $name (already gc'ed)...\n");
                        continue;
                    }

                    fwrite(STDERR, "GC'ing $name...\n");
                    $component->gc(force: true);

                    $db = $component->db;
                    if (
                        $db instanceof Connection &&
                        $db->driverName === 'pgsql'
                    ) {
                        fwrite(STDERR, "VACUUM'ing $tableName...\n");
                        $db->createCommand(sprintf('VACUUM ( ANALYZE ) %s', $db->quoteTableName($tableName)))
                            ->execute();
                    }

                    $gcedTables[] = $tableName;
                }
            }
        }

        return ExitCode::OK;
    }
}
