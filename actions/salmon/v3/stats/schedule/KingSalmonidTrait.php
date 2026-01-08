<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\schedule;

use app\models\SalmonKing3;
use app\models\SalmonSchedule3;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use const SORT_ASC;
use const SORT_DESC;

trait KingSalmonidTrait
{
    /**
     * @return array<int, array{
     *   king_id: int,
     *   appearances: int,
     *   defeated: int,
     *   gold_scale: ?int,
     *   silver_scale: ?int,
     *   bronze_scale: ?int
     * }>
     */
    private function getKingStats(Connection $db, User $user, ?SalmonSchedule3 $schedule): array
    {
        return ArrayHelper::index(
            (new Query())
                ->select([
                    'king_id' => '{{%salmon3}}.[[king_salmonid_id]]',
                    'appearances' => 'COUNT(*)',
                    'defeated' => 'SUM(CASE WHEN [[clear_extra]] = TRUE THEN 1 ELSE 0 END)',
                    'gold_scale' => 'SUM({{%salmon3}}.[[gold_scale]])',
                    'silver_scale' => 'SUM({{%salmon3}}.[[silver_scale]])',
                    'bronze_scale' => 'SUM({{%salmon3}}.[[bronze_scale]])',
                ])
                ->from('{{%salmon3}}')
                ->andWhere(['and',
                    [
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon3}}.[[user_id]]' => $user->id,
                    ],
                    $schedule
                        ? ['{{%salmon3}}.[[schedule_id]]' => $schedule->id]
                        : ['{{%salmon3}}.[[is_eggstra_work]]' => false],
                    ['not', ['{{%salmon3}}.[[king_salmonid_id]]' => null]],
                    ['not', ['{{%salmon3}}.[[clear_extra]]' => null]],
                ])
                ->groupBy([
                    '{{%salmon3}}.[[king_salmonid_id]]',
                ])
                ->orderBy([
                    'appearances' => SORT_DESC,
                    'king_id' => SORT_ASC,
                ])
                ->all($db),
            'king_id',
        );
    }

    /**
     * @return array<int, SalmonKing3>
     */
    private function getKings(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonKing3::find()->orderBy(['id' => SORT_ASC])->all(),
            'id',
        );
    }
}
