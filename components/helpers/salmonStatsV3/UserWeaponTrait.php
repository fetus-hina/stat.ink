<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\salmonStatsV3;

use Throwable;
use Yii;
use app\models\Salmon3UserStatsWeapon;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;

use function array_keys;
use function array_map;
use function implode;
use function vsprintf;

trait UserWeaponTrait
{
    protected static function createUserWeaponStats(Connection $db, User $user): bool
    {
        try {
            $select = (new Query())
                ->select([
                    'user_id' => '{{%salmon3}}.[[user_id]]',
                    'weapon_id' => '{{%salmon_player_weapon3}}.[[weapon_id]]',
                    'total_waves' => vsprintf('SUM(CASE %s END) + SUM(CASE %s END)', [
                        implode(' ', [
                            'WHEN {{%salmon_player_weapon3}}.[[wave]] BETWEEN 1 AND 3 THEN 1',
                            'ELSE 0',
                        ]),
                        implode(' ', [
                            'WHEN {{%salmon_player_weapon3}}.[[wave]] <= 3 THEN 0',
                            'WHEN {{%salmon3}}.[[king_salmonid_id]] IS NULL THEN 0',
                            'WHEN {{%salmon3}}.[[clear_extra]] IS NULL THEN 0',
                            'ELSE 1',
                        ]),
                    ]),
                    'normal_waves' => vsprintf('SUM(CASE %s END)', [
                        implode(' ', [
                            'WHEN {{%salmon_player_weapon3}}.[[wave]] BETWEEN 1 AND 3 THEN 1',
                            'ELSE 0',
                        ]),
                    ]),
                    'normal_waves_cleared' => vsprintf('SUM(CASE %s END)', [
                        implode(' ', [
                            vsprintf('WHEN (%s) AND (%s) THEN 1', [
                                '{{%salmon_player_weapon3}}.[[wave]] BETWEEN 1 AND 3',
                                '{{%salmon3}}.[[clear_waves]] >= 3',
                            ]),
                            'ELSE 0',
                        ]),
                    ]),
                    'xtra_waves' => vsprintf('SUM(CASE %s END)', [
                        implode(' ', [
                            'WHEN {{%salmon_player_weapon3}}.[[wave]] <= 3 THEN 0',
                            'WHEN {{%salmon3}}.[[king_salmonid_id]] IS NULL THEN 0',
                            'WHEN {{%salmon3}}.[[clear_extra]] IS NULL THEN 0',
                            'ELSE 1',
                        ]),
                    ]),
                    'xtra_waves_cleared' => vsprintf('SUM(CASE %s END)', [
                        implode(' ', [
                            'WHEN {{%salmon_player_weapon3}}.[[wave]] <= 3 THEN 0',
                            'WHEN {{%salmon3}}.[[king_salmonid_id]] IS NULL THEN 0',
                            'WHEN {{%salmon3}}.[[clear_extra]] <> TRUE THEN 0',
                            'ELSE 1',
                        ]),
                    ]),
                ])
                ->from('{{%salmon3}}')
                ->innerJoin(
                    '{{%salmon_player3}}',
                    implode(' AND ', [
                        '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                        '{{%salmon_player3}}.[[is_me]] = TRUE',
                    ]),
                )
                ->innerJoin(
                    '{{%salmon_player_weapon3}}',
                    '{{%salmon_player3}}.[[id]] = {{%salmon_player_weapon3}}.[[player_id]]',
                )
                ->andWhere(['and',
                    [
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_eggstra_work]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon3}}.[[user_id]]' => $user->id,
                    ],
                    ['between', '{{%salmon_player_weapon3}}.[[wave]]', 1, 3 + 1],
                    ['not', ['{{%salmon_player_weapon3}}.[[weapon_id]]' => null]],
                ])
                ->groupBy([
                    '{{%salmon3}}.[[user_id]]',
                    '{{%salmon_player_weapon3}}.[[weapon_id]]',
                ]);

            Salmon3UserStatsWeapon::deleteAll(['user_id' => $user->id]);
            $insertSql = vsprintf('INSERT INTO %s (%s) %s', [
                $db->quoteTableName(Salmon3UserStatsWeapon::tableName()),
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
