<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\components\helpers\UserPlayedWith3Helper;
use app\models\User;
use yii\console\Controller;

use function fprintf;
use function fwrite;

use const STDERR;

final class UserPlayedWith3Controller extends Controller
{
    public function actionRebuildBattle(int $userId): int
    {
        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            fwrite(STDERR, "User unknown\n");
            return 1;
        }

        fprintf(STDERR, "User id=%d, screen_name=%s\n", $user->id, $user->screen_name);
        if (!UserPlayedWith3Helper::rebuildUserBattle($user)) {
            fwrite(STDERR, "Failed to update\n");
            return 1;
        }

        fwrite(STDERR, "OK\n");
        return 0;
    }

    public function actionRebuildSalmon(int $userId): int
    {
        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            fwrite(STDERR, "User unknown\n");
            return 1;
        }

        fprintf(STDERR, "User id=%d, screen_name=%s\n", $user->id, $user->screen_name);
        if (!UserPlayedWith3Helper::rebuildUserSalmon($user)) {
            fwrite(STDERR, "Failed to update\n");
            return 1;
        }

        fwrite(STDERR, "OK\n");
        return 0;
    }
}
