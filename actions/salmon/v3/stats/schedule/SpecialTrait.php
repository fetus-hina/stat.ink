<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\schedule;

use app\models\SalmonSchedule3;
use app\models\Special3;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use function implode;
use function sprintf;

use const SORT_ASC;

trait SpecialTrait
{
    private function getSpecialStats(Connection $db, User $user, SalmonSchedule3 $schedule): array
    {
        $waves = $schedule->is_eggstra_work ? 5 : 3;
        return ArrayHelper::index(
            (new Query())
                ->select([
                    'special_id' => '{{%salmon_player3}}.[[special_id]]',
                    'count' => 'COUNT(*)',
                    'cleared' => sprintf(
                        'SUM(CASE WHEN [[clear_waves]] >= %d THEN 1 ELSE 0 END)',
                        $waves,
                    ),
                    'avg_waves' => sprintf(
                        'AVG(CASE WHEN [[clear_waves]] >= %1$d THEN %1$d ELSE [[clear_waves]] END)',
                        $waves,
                    ),
                    'max_golden' => 'MAX({{%salmon3}}.[[golden_eggs]])',
                    'total_golden' => 'SUM({{%salmon3}}.[[golden_eggs]])',
                    'avg_golden' => 'AVG({{%salmon3}}.[[golden_eggs]])',
                    'max_power' => 'MAX({{%salmon3}}.[[power_eggs]])',
                    'total_power' => 'SUM({{%salmon3}}.[[power_eggs]])',
                    'avg_power' => 'AVG({{%salmon3}}.[[power_eggs]])',
                    'total_rescues' => 'SUM({{%salmon_player3}}.[[rescue]])',
                    'avg_rescues' => 'AVG({{%salmon_player3}}.[[rescue]])',
                    'total_rescued' => 'SUM({{%salmon_player3}}.[[rescued]])',
                    'avg_rescued' => 'AVG({{%salmon_player3}}.[[rescued]])',
                    'total_defeat_boss' => 'SUM({{%salmon_player3}}.[[defeat_boss]])',
                    'avg_defeat_boss' => 'AVG({{%salmon_player3}}.[[defeat_boss]])',
                ])
                ->from('{{%salmon3}}')
                ->innerJoin(
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
                        '{{%salmon3}}.[[schedule_id]]' => $schedule->id,
                        '{{%salmon3}}.[[user_id]]' => $user->id,
                    ],
                    ['not', ['{{%salmon_player3}}.[[special_id]]' => null]],
                ])
                ->groupby([
                    '{{%salmon_player3}}.[[special_id]]',
                ])
                ->all($db),
            'special_id',
        );
    }

    /**
     * @return array<int, Special3>
     */
    private function getSpecials(Connection $db): array
    {
        return ArrayHelper::index(
            Special3::find()->orderBy(['rank' => SORT_ASC])->all(),
            'id',
        );
    }
}
