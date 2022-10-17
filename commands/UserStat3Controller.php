<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\components\helpers\UserStatsV3;
use app\models\User;
use yii\console\Controller;

use const STDERR;

final class UserStat3Controller extends Controller
{
    public $defaultAction = 'update';

    public function actionUpdate($id): int
    {
        $intId = \filter_var($id, FILTER_VALIDATE_INT);
        if (\is_int($intId)) {
            $user = User::findOne(['id' => $intId]);
        } else {
            $user = User::findOne(['screen_name' => $id]);
        }

        if (!$user) {
            \fwrite(STDERR, "User unknown\n");
            return 1;
        }

        \fprintf(STDERR, "User id=%d, screen_name=%s\n", $user->id, $user->screen_name);
        if (!UserStatsV3::create($user)) {
            \fwrite(STDERR, "Failed to update\n");
            return 1;
        }

        \fwrite(STDERR, "OK\n");
        return 0;
    }
}
