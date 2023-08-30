<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\schedule;

use app\components\helpers\TypeHelper;
use app\models\SalmonSchedule3;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;

use function implode;
use function sprintf;

trait AbstractTrait
{
    private function getStats(Connection $db, User $user, ?SalmonSchedule3 $schedule): array
    {
        $waves = $schedule?->is_eggstra_work ? 5 : 3;
        return TypeHelper::array(
            (new Query())
                ->select([
                    'count' => 'COUNT(*)',
                    'cleared' => sprintf(
                        'SUM(CASE WHEN [[clear_waves]] >= %d THEN 1 ELSE 0 END)',
                        $waves,
                    ),
                    'avg_waves' => sprintf(
                        'AVG(CASE WHEN [[clear_waves]] >= %1$d THEN %1$d ELSE [[clear_waves]] END)',
                        $waves,
                    ),
                    'max_danger_rate' => sprintf(
                        'MAX(CASE %s END)',
                        implode(' ', [
                            sprintf('WHEN [[clear_waves]] >= %d THEN [[danger_rate]]', $waves),
                            'ELSE NULL',
                        ]),
                    ),
                    'king_appears' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            sprintf(
                                'WHEN [[clear_waves]] >= %d AND [[king_salmonid_id]] IS NOT NULL THEN 1',
                                $waves,
                            ),
                            'ELSE 0',
                        ]),
                    ),
                    'king_defeated' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            sprintf(
                                'WHEN %s THEN 1',
                                implode(' AND ', [
                                    sprintf('[[clear_waves]] >= %d', $waves),
                                    '[[king_salmonid_id]] IS NOT NULL',
                                    '[[clear_extra]] = TRUE',
                                ]),
                            ),
                            'ELSE 0',
                        ]),
                    ),
                    'total_gold_scale' => 'SUM({{%salmon3}}.[[gold_scale]])',
                    'total_silver_scale' => 'SUM({{%salmon3}}.[[silver_scale]])',
                    'total_bronze_scale' => 'SUM({{%salmon3}}.[[bronze_scale]])',
                    'max_golden' => 'MAX({{%salmon3}}.[[golden_eggs]])',
                    'total_golden' => 'SUM({{%salmon3}}.[[golden_eggs]])',
                    'avg_golden' => 'AVG({{%salmon3}}.[[golden_eggs]])',
                    'max_golden_individual' => 'MAX({{%salmon_player3}}.[[golden_eggs]])',
                    'total_golden_individual' => 'SUM({{%salmon_player3}}.[[golden_eggs]])',
                    'avg_golden_individual' => 'AVG({{%salmon_player3}}.[[golden_eggs]])',
                    'max_power' => 'MAX({{%salmon3}}.[[power_eggs]])',
                    'total_power' => 'SUM({{%salmon3}}.[[power_eggs]])',
                    'avg_power' => 'AVG({{%salmon3}}.[[power_eggs]])',
                    'max_power_individual' => 'MAX({{%salmon_player3}}.[[power_eggs]])',
                    'total_power_individual' => 'SUM({{%salmon_player3}}.[[power_eggs]])',
                    'avg_power_individual' => 'AVG({{%salmon_player3}}.[[power_eggs]])',
                    'total_rescues' => 'SUM({{%salmon_player3}}.[[rescue]])',
                    'avg_rescues' => 'AVG({{%salmon_player3}}.[[rescue]])',
                    'total_rescued' => 'SUM({{%salmon_player3}}.[[rescued]])',
                    'avg_rescued' => 'AVG({{%salmon_player3}}.[[rescued]])',
                    'total_defeat_boss' => 'SUM({{%salmon_player3}}.[[defeat_boss]])',
                    'avg_defeat_boss' => 'AVG({{%salmon_player3}}.[[defeat_boss]])',
                ])
                ->from('{{%salmon3}}')
                ->leftJoin(
                    '{{%salmon_player3}}',
                    implode(' AND ', [
                        '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                        '{{%salmon_player3}}.[[is_me]] = TRUE',
                    ]),
                )
                ->andWhere(['and',
                    [
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon3}}.[[user_id]]' => $user->id,
                    ],
                    ['not', ['{{%salmon3}}.[[clear_waves]]' => null]],
                    ['not', ['{{%salmon3}}.[[danger_rate]]' => null]],
                ])
                ->andWhere(
                    $schedule
                        ? ['{{%salmon3}}.[[schedule_id]]' => $schedule->id]
                        : ['{{%salmon3}}.[[is_eggstra_work]]' => false],
                )
                ->one($db),
        );
    }
}
