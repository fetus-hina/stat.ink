<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\userPlayedWith3;

use app\models\Battle3;
use app\models\Salmon3;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;

use function array_keys;
use function array_map;
use function implode;
use function sprintf;
use function vsprintf;

trait UpdateTrait
{
    // TODO: tricolor
    private static function updateBattleImpl(Connection $db, User $user, ?Battle3 $battle): true
    {
        $select = (new Query())
            ->select([
                'user_id' => '{{%battle3}}.[[user_id]]',
                'name' => '{{%battle_player3}}.[[name]]',
                'number' => '{{%battle_player3}}.[[number]]',
                'ref_id' => 'calc_played_with3_id({{%battle_player3}}.[[name]], {{%battle_player3}}.[[number]])',
                'count' => 'COUNT(*)',
                'disconnect' => 'SUM(CASE WHEN {{%battle_player3}}.[[is_disconnected]] THEN 1 ELSE 0 END)',
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => $user->id,
                    '{{%battle_player3}}.[[is_me]]' => false,
                ],
                ['not', ['{{%battle_player3}}.[[name]]' => null]],
                ['not', ['{{%battle_player3}}.[[number]]' => null]],
            ])
            ->groupBy([
                '{{%battle3}}.[[user_id]]',
                '{{%battle_player3}}.[[name]]',
                '{{%battle_player3}}.[[number]]',
            ]);

        if ($battle) {
            $subQuery = (new Query())
                ->select(['name', 'number'])
                ->from('{{%battle_player3}}')
                ->andWhere([
                    '{{%battle_player3}}.[[battle_id]]' => $battle->id,
                    '{{%battle_player3}}.[[is_me]]' => false,
                ])
                ->andWhere(['and',
                    ['not', ['{{%battle_player3}}.[[name]]' => null]],
                    ['not', ['{{%battle_player3}}.[[number]]' => null]],
                ]);

            $select->innerJoin(
                ['tTargetPlayers' => $subQuery],
                implode(' AND ', [
                    '{{%battle_player3}}.[[name]] = {{tTargetPlayers}}.[[name]]',
                    '{{%battle_player3}}.[[number]] = {{tTargetPlayers}}.[[number]]',
                ]),
            );
        }

        $sql = vsprintf('INSERT INTO %s (%s) %s ON CONFLICT ON CONSTRAINT %s DO UPDATE SET %s', [
            $db->quoteTableName('{{%battle3_played_with}}'),
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
            $db->quoteColumnName('battle3_played_with_pkey'),
            implode(
                ', ',
                array_map(
                    fn (string $name): string => sprintf('%1$s = EXCLUDED.%1$s', $db->quoteColumnName($name)),
                    ['count', 'disconnect'],
                ),
            ),
        ]);

        $db->createCommand($sql)->execute();

        return true;
    }

    private static function updateSalmonImpl(Connection $db, User $user, ?Salmon3 $salmon): bool
    {
        $select = (new Query())
            ->select([
                'user_id' => '{{%salmon3}}.[[user_id]]',
                'name' => '{{%salmon_player3}}.[[name]]',
                'number' => '{{%salmon_player3}}.[[number]]',
                'ref_id' => 'calc_played_with3_id({{%salmon_player3}}.[[name]], {{%salmon_player3}}.[[number]])',
                'count' => 'COUNT(*)',
                'disconnect' => 'SUM(CASE WHEN {{%salmon_player3}}.[[is_disconnected]] THEN 1 ELSE 0 END)',
            ])
            ->from('{{%salmon3}}')
            ->innerJoin('{{%salmon_player3}}', '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]')
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                    '{{%salmon_player3}}.[[is_me]]' => false,
                ],
                ['not', ['{{%salmon_player3}}.[[name]]' => null]],
                ['not', ['{{%salmon_player3}}.[[number]]' => null]],
            ])
            ->groupBy([
                '{{%salmon3}}.[[user_id]]',
                '{{%salmon_player3}}.[[name]]',
                '{{%salmon_player3}}.[[number]]',
            ]);

        if ($salmon) {
            $subQuery = (new Query())
                ->select(['name', 'number'])
                ->from('{{%salmon_player3}}')
                ->andWhere([
                    '{{%salmon_player3}}.[[salmon_id]]' => $salmon->id,
                    '{{%salmon_player3}}.[[is_me]]' => false,
                ])
                ->andWhere(['and',
                    ['not', ['{{%salmon_player3}}.[[name]]' => null]],
                    ['not', ['{{%salmon_player3}}.[[number]]' => null]],
                ]);

            $select->innerJoin(
                ['tTargetPlayers' => $subQuery],
                implode(' AND ', [
                    '{{%salmon_player3}}.[[name]] = {{tTargetPlayers}}.[[name]]',
                    '{{%salmon_player3}}.[[number]] = {{tTargetPlayers}}.[[number]]',
                ]),
            );
        }

        $sql = vsprintf('INSERT INTO %s (%s) %s ON CONFLICT ON CONSTRAINT %s DO UPDATE SET %s', [
            $db->quoteTableName('{{%salmon3_played_with}}'),
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
            $db->quoteColumnName('salmon3_played_with_pkey'),
            implode(
                ', ',
                array_map(
                    fn (string $name): string => sprintf('%1$s = EXCLUDED.%1$s', $db->quoteColumnName($name)),
                    ['count', 'disconnect'],
                ),
            ),
        ]);

        $db->createCommand($sql)->execute();

        return true;
    }
}
