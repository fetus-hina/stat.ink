<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\components\helpers\SalmonStatsV3;
use app\components\helpers\UserStatsV3;
use app\components\jobs\SalmonStatsJob;
use app\components\jobs\UserStatsJob;
use app\models\User;
use yii\console\Controller;
use yii\db\Query;

use function filter_var;
use function fprintf;
use function fwrite;
use function is_int;

use const FILTER_VALIDATE_INT;
use const SORT_ASC;
use const STDERR;

final class UserStat3Controller extends Controller
{
    public $defaultAction = 'update';

    public function actionUpdate($id): int
    {
        $intId = filter_var($id, FILTER_VALIDATE_INT);
        if (is_int($intId)) {
            $user = User::findOne(['id' => $intId]);
        } else {
            $user = User::findOne(['screen_name' => $id]);
        }

        if (!$user) {
            fwrite(STDERR, "User unknown\n");
            return 1;
        }

        fprintf(STDERR, "User id=%d, screen_name=%s\n", $user->id, $user->screen_name);
        if (!UserStatsV3::create($user)) {
            fwrite(STDERR, "Failed to update\n");
            return 1;
        }

        fwrite(STDERR, "OK\n");
        return 0;
    }

    public function actionSalmonUpdate($id): int
    {
        $intId = filter_var($id, FILTER_VALIDATE_INT);
        if (is_int($intId)) {
            $user = User::findOne(['id' => $intId]);
        } else {
            $user = User::findOne(['screen_name' => $id]);
        }

        if (!$user) {
            fwrite(STDERR, "User unknown\n");
            return 1;
        }

        fprintf(STDERR, "User id=%d, screen_name=%s\n", $user->id, $user->screen_name);
        if (!SalmonStatsV3::create($user)) {
            fwrite(STDERR, "Failed to update\n");
            return 1;
        }

        fwrite(STDERR, "OK\n");
        return 0;
    }

    public function actionRegenAll(): int
    {
        $this->actionBattleRegenAll();
        $this->actionSalmonRegenAll();

        return 0;
    }

    public function actionBattleRegenAll(): int
    {
        $query = (new Query())
            ->select(['user_id'])
            ->from('{{%battle3}}')
            ->andWhere(['is_deleted' => false])
            ->groupBy(['user_id'])
            ->orderBy(['user_id' => SORT_ASC]);
        foreach ($query->each() as $row) {
            fprintf(STDERR, "[regen-all / battle] user id=%d\n", $row['user_id']);

            UserStatsJob::pushQueue3(
                User::find()->andWhere(['id' => $row['user_id']])->limit(1)->one(),
            );
        }

        return 0;
    }

    public function actionSalmonRegenAll(): int
    {
        $query = (new Query())
            ->select(['user_id'])
            ->from('{{%salmon3}}')
            ->andWhere(['is_deleted' => false])
            ->groupBy(['user_id'])
            ->orderBy(['user_id' => SORT_ASC]);
        foreach ($query->each() as $row) {
            fprintf(STDERR, "[regen-all / salmon] user id=%d\n", $row['user_id']);

            SalmonStatsJob::pushQueue3(
                User::find()->andWhere(['id' => $row['user_id']])->limit(1)->one(),
            );
        }

        return 0;
    }
}
