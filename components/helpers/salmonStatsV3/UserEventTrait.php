<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\salmonStatsV3;

use Throwable;
use Yii;
use app\models\Salmon3UserStatsEvent;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;

use function array_keys;
use function array_map;
use function implode;
use function vsprintf;

trait UserEventTrait
{
    protected static function createUserEventStats(Connection $db, User $user): bool
    {
        try {
            $select = (new Query())
                ->select([
                    'user_id' => '{{%salmon3}}.[[user_id]]',
                    'map_id' => '{{%salmon3}}.[[stage_id]]',
                    'tide_id' => '{{%salmon_wave3}}.[[tide_id]]',
                    'event_id' => '{{%salmon_wave3}}.[[event_id]]',
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
                        '{{%salmon3}}.[[is_big_run]]' => false,
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_eggstra_work]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon3}}.[[user_id]]' => $user->id,
                    ],
                    ['between', '{{%salmon_wave3}}.[[wave]]', 1, 3],
                    ['not', ['{{%salmon3}}.[[stage_id]]' => null]],
                    ['not', ['{{%salmon_wave3}}.[[golden_delivered]]' => null]],
                    ['not', ['{{%salmon_wave3}}.[[golden_quota]]' => null]],
                    ['not', ['{{%salmon_wave3}}.[[tide_id]]' => null]],
                ])
                ->groupBy([
                    '{{%salmon3}}.[[user_id]]',
                    '{{%salmon3}}.[[stage_id]]',
                    '{{%salmon_wave3}}.[[tide_id]]',
                    '{{%salmon_wave3}}.[[event_id]]',
                ]);

            Salmon3UserStatsEvent::deleteAll(['user_id' => $user->id]);
            $insertSql = vsprintf('INSERT INTO %s (%s) %s', [
                $db->quoteTableName(Salmon3UserStatsEvent::tableName()),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]);
            $db->createCommand($insertSql)->execute();

            return true;
        } catch (Throwable $e) {
            Yii::error(
                vsprintf('Catch %s, message=%s', [
                    $e::class,
                    $e->getMessage(),
                ]),
                __METHOD__,
            );
            $db->transaction->rollBack();
            return false;
        }
    }
}
