<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\schedule;

use app\models\SalmonBoss3;
use app\models\SalmonSchedule3;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use const SORT_ASC;
use const SORT_DESC;

trait BossSalmonidTrait
{
    /**
     * @return array<int, array{boss_id: int, appearances: int, defeated: int, defeated_by_me: int}>
     */
    private function getBossStats(Connection $db, User $user, ?SalmonSchedule3 $schedule): array
    {
        return ArrayHelper::index(
            (new Query())
                ->select([
                    'boss_id' => '{{%salmon_boss_appearance3}}.[[boss_id]]',
                    'appearances' => 'SUM({{%salmon_boss_appearance3}}.[[appearances]])',
                    'defeated' => 'SUM({{%salmon_boss_appearance3}}.[[defeated]])',
                    'defeated_by_me' => 'SUM({{%salmon_boss_appearance3}}.[[defeated_by_me]])',
                ])
                ->from('{{%salmon3}}')
                ->innerJoin(
                    '{{%salmon_boss_appearance3}}',
                    '{{%salmon3}}.[[id]] = {{%salmon_boss_appearance3}}.[[salmon_id]]',
                )
                ->andWhere(['and',
                    [
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon3}}.[[user_id]]' => $user->id,
                    ],
                    $schedule
                        ? ['{{%salmon3}}.[[schedule_id]]' => $schedule->id]
                        : ['{{%salmon3}}.[[is_eggstra_work]]' => false],
                ])
                ->groupBy([
                    '{{%salmon_boss_appearance3}}.[[boss_id]]',
                ])
                ->orderBy([
                    'appearances' => SORT_DESC,
                    'boss_id' => SORT_ASC,
                ])
                ->all($db),
            'boss_id',
        );
    }

    /**
     * @return array<int, SalmonBoss3>
     */
    private function getBosses(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonBoss3::find()->orderBy(['id' => SORT_ASC])->all(),
            'id',
        );
    }
}
