<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\schedule;

use app\models\SalmonSchedule3;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;

use function implode;
use function sprintf;

use const SORT_ASC;
use const SORT_DESC;

/**
 * @phpstan-type PlayerStats array{
 *   name: string,
 *   number: string,
 *   jobs: int,
 *   cleared: int,
 *   waves: int,
 *   max_danger_rate: int|float|numeric-string|null
 *   clear_danger_rate: int|float|numeric-string|null
 *   total_golden_result: int|null
 *   avg_golden_result: int|float|numeric-string|null
 *   max_golden_result: int|float|numeric-string|null
 *   total_golden: int|null
 *   avg_golden: int|float|numeric-string|null
 *   max_golden: int|null
 *   total_rescue: int|null
 *   avg_rescue: int|float|numeric-string|null
 *   total_rescued: int|null
 *   avg_rescued: int|float|numeric-string|null
 * }
 */
trait PlayerTrait
{
    /**
     * @return PlayerStats[]
     */
    private function getPlayerStats(Connection $db, User $user, SalmonSchedule3 $schedule): array
    {
        $waves = $schedule->is_eggstra_work ? 5 : 3;
        return (new Query())
            ->select([
                'name' => '{{%salmon_player3}}.[[name]]',
                'number' => '{{%salmon_player3}}.[[number]]',
                'jobs' => 'COUNT(*)',
                'cleared' => sprintf(
                    'SUM(CASE %s END)',
                    implode(' ', [
                        "WHEN {{%salmon3}}.[[clear_waves]] >= {$waves} THEN 1",
                        'ELSE 0',
                    ]),
                ),
                'waves' => sprintf(
                    'SUM(CASE %s END)',
                    implode(' ', [
                        "WHEN {{%salmon3}}.[[clear_waves]] >= {$waves} THEN {$waves}",
                        'ELSE {{%salmon3}}.[[clear_waves]] + 1',
                    ]),
                ),
                'max_danger_rate' => 'MAX({{%salmon3}}.[[danger_rate]])',
                'clear_danger_rate' => sprintf(
                    'MAX(CASE %s END)',
                    implode(' ', [
                        "WHEN {{%salmon3}}.[[clear_waves]] >= {$waves} THEN {{%salmon3}}.[[danger_rate]]",
                        'ELSE NULL',
                    ]),
                ),
                'total_golden_result' => 'SUM({{%salmon3}}.[[golden_eggs]])',
                'avg_golden_result' => 'AVG({{%salmon3}}.[[golden_eggs]])',
                'max_golden_result' => 'MAX({{%salmon3}}.[[golden_eggs]])',
                'total_golden' => 'SUM({{%salmon_player3}}.[[golden_eggs]])',
                'avg_golden' => 'AVG({{%salmon_player3}}.[[golden_eggs]])',
                'max_golden' => 'MAX({{%salmon_player3}}.[[golden_eggs]])',
                'total_rescue' => 'SUM({{%salmon_player3}}.[[rescue]])',
                'avg_rescue' => 'AVG({{%salmon_player3}}.[[rescue]])',
                'total_rescued' => 'SUM({{%salmon_player3}}.[[rescued]])',
                'avg_rescued' => 'AVG({{%salmon_player3}}.[[rescued]])',
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon_player3}}',
                implode(' AND ', [
                    '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                    '{{%salmon_player3}}.[[is_me]] = FALSE',
                ]),
            )
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                    '{{%salmon3}}.[[schedule_id]]' => $schedule->id,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                ],
                ['not', ['{{%salmon_player3}}.[[name]]' => null]],
                ['not', ['{{%salmon_player3}}.[[number]]' => null]],
            ])
            ->groupBy([
                '{{%salmon_player3}}.[[name]]',
                '{{%salmon_player3}}.[[number]]',
            ])
            ->orderBy([
                'jobs' => SORT_DESC,
                'waves' => SORT_DESC,
                'clear_danger_rate' => SORT_DESC,
                'max_danger_rate' => SORT_DESC,
                'name' => SORT_ASC,
                'number' => SORT_ASC,
            ])
            ->limit(200)
            ->all($db);
    }
}
