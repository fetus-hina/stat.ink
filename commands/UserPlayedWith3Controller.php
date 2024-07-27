<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\components\helpers\UserPlayedWith3Helper;
use app\models\Battle3;
use app\models\Salmon3;
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

    public function actionUpdateBattle(int $userId, int|string $battleId): int
    {
        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            fwrite(STDERR, "User unknown\n");
            return 1;
        }

        $intBattleId = filter_var($battleId, FILTER_VALIDATE_INT);
        if (is_int($intBattleId)) {
            $battle = Battle3::findOne([
                'id' => $intBattleId,
                'is_deleted' => false,
                'user_id' => $user->id,
            ]);
            if (!$battle) {
                fwrite(STDERR, "Battle not found\n");
                return 1;
            }
        } else {
            $battle = Battle3::findOne([
                'uuid' => $battleId,
                'is_deleted' => false,
                'user_id' => $user->id,
            ]);
            if (!$battle) {
                fwrite(STDERR, "Battle not found\n");
                return 1;
            }
        }

        fprintf(STDERR, "User id=%d, screen_name=%s\n", $user->id, $user->screen_name);
        fprintf(STDERR, "Battle id=%d, uuid=%s\n", $battle->id, $battle->uuid);
        if (!UserPlayedWith3Helper::updateBattle($user, $battle)) {
            fwrite(STDERR, "Failed to update\n");
            return 1;
        }

        fwrite(STDERR, "OK\n");
        return 0;
    }

    public function actionUpdateSalmon(int $userId, int|string $salmonId): int
    {
        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            fwrite(STDERR, "User unknown\n");
            return 1;
        }

        $intSalmonId = filter_var($salmonId, FILTER_VALIDATE_INT);
        if (is_int($intSalmonId)) {
            $salmon = Salmon3::findOne([
                'id' => $intSalmonId,
                'is_deleted' => false,
                'user_id' => $user->id,
            ]);
            if (!$salmon) {
                fwrite(STDERR, "Salmon not found\n");
                return 1;
            }
        } else {
            $salmon = Salmon3::findOne([
                'uuid' => $salmonId,
                'is_deleted' => false,
                'user_id' => $user->id,
            ]);
            if (!$salmon) {
                fwrite(STDERR, "Salmon not found\n");
                return 1;
            }
        }

        fprintf(STDERR, "User id=%d, screen_name=%s\n", $user->id, $user->screen_name);
        fprintf(STDERR, "Salmon id=%d, uuid=%s\n", $salmon->id, $salmon->uuid);
        if (!UserPlayedWith3Helper::updateSalmon($user, $salmon)) {
            fwrite(STDERR, "Failed to update\n");
            return 1;
        }

        fwrite(STDERR, "OK\n");
        return 0;
    }
}
