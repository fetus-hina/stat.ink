<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\schedule;

use app\models\SalmonEvent3;
use app\models\SalmonSchedule3;
use app\models\SalmonWaterLevel2;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use function implode;
use function vsprintf;

use const SORT_ASC;

/**
 * @phpstan-type EventStats array<int, array<int, array{
 *   tide_id: int,
 *   event_id: int,
 *   waves: int,
 *   cleared: int,
 *   total_quota: int,
 *   total_delivered: int
 * }>>
 */
trait EventTrait
{
    /**
     * @phpstan-return EventStats
     */
    private function getEventStats(Connection $db, User $user, ?SalmonSchedule3 $schedule): array
    {
        $waves = $schedule?->is_eggstra_work ? 5 : 3;

        $data = (new Query())
            ->select([
                'tide_id' => '{{%salmon_wave3}}.[[tide_id]]',
                'event_id' => 'COALESCE({{%salmon_wave3}}.[[event_id]], 0)',
                'waves' => 'COUNT(*)',
                'cleared' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%salmon3}}.[[clear_waves]] >= {{%salmon_wave3}}.[[wave]] THEN 1',
                        'ELSE 0',
                    ]),
                ]),
                'total_quota' => 'SUM({{%salmon_wave3}}.[[golden_quota]])',
                'total_delivered' => 'SUM({{%salmon_wave3}}.[[golden_delivered]])',
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon_wave3}}',
                '{{%salmon3}}.[[id]] = {{%salmon_wave3}}.[[salmon_id]]',
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
                ['between', '{{%salmon_wave3}}.[[wave]]', 1, $waves],
                ['not', ['{{%salmon_wave3}}.[[tide_id]]' => null]],
                ['not', ['{{%salmon_wave3}}.[[golden_quota]]' => null]],
                ['not', ['{{%salmon_wave3}}.[[golden_delivered]]' => null]],
            ])
            ->groupBy([
                '{{%salmon_wave3}}.[[tide_id]]',
                '{{%salmon_wave3}}.[[event_id]]',
            ])
            ->all($db);

        return ArrayHelper::index(
            $data,
            'tide_id',
            'event_id',
        );
    }

    /**
     * @return array<int, SalmonWaterLevel2>
     */
    private function getTides(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonWaterLevel2::find()
                ->orderBy(['id' => SORT_ASC])
                ->all(),
            'id',
        );
    }

    /**
     * @return array<int, SalmonEvent3>
     */
    private function getEvents(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonEvent3::find()
                ->orderBy(['id' => SORT_ASC])
                ->all(),
            'id',
        );
    }
}
