<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\userPlayedWith3;

use app\components\helpers\TypeHelper;
use app\models\Battle3;
use app\models\Rule3;
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
    private static function updateBattleImpl(Connection $db, User $user, ?Battle3 $battle): true
    {
        $tricolor = TypeHelper::instanceOf(
            Rule3::findOne(['key' => 'tricolor']),
            Rule3::class,
        );

        $name = vsprintf('(CASE %s WHEN %d THEN %s ELSE %s END)', [
            '{{%battle3}}.[[rule_id]]',
            $tricolor->id,
            '{{p2}}.[[name]]',
            '{{p1}}.[[name]]',
        ]);

        $number = vsprintf('(CASE %s WHEN %d THEN %s ELSE %s END)', [
            '{{%battle3}}.[[rule_id]]',
            $tricolor->id,
            '{{p2}}.[[number]]',
            '{{p1}}.[[number]]',
        ]);

        $select = (new Query())
            ->select([
                'user_id' => '{{%battle3}}.[[user_id]]',
                'name' => $name,
                'number' => $number,
                'ref_id' => vsprintf('MAX(CASE %s WHEN %d THEN %s ELSE %s END)', [
                    '{{%battle3}}.[[rule_id]]',
                    $tricolor->id,
                    'calc_played_with3_id({{p2}}.[[name]], {{p2}}.[[number]])',
                    'calc_played_with3_id({{p1}}.[[name]], {{p1}}.[[number]])',
                ]),
                'count' => 'COUNT(*)',
                'disconnect' => vsprintf('SUM(%s)', [
                    vsprintf('CASE WHEN %s THEN 1 WHEN %s THEN 1 ELSE 0 END', [
                        '({{p1}}.[[id]] IS NOT NULL AND {{p1}}.[[is_disconnected]])',
                        '({{p1}}.[[id]] IS NOT NULL AND {{p2}}.[[is_disconnected]])',
                    ]),
                ]),
            ])
            ->from('{{%battle3}}')
            ->leftJoin(
                ['p1' => '{{%battle_player3}}'],
                ['and',
                    ['<>', '{{%battle3}}.[[rule_id]]', $tricolor->id],
                    '{{%battle3}}.[[id]] = {{p1}}.[[battle_id]]',
                ],
            )
            ->leftJoin(
                ['p2' => '{{%battle_tricolor_player3}}'],
                ['and',
                    ['{{%battle3}}.[[rule_id]]' => $tricolor->id],
                    '{{%battle3}}.[[id]] = {{p2}}.[[battle_id]]',
                ],
            )
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => $user->id,
                ],
                vsprintf('((%s) OR (%s))', [
                    implode(' AND ', [
                        '{{p1}}.[[id]] IS NOT NULL',
                        '{{p1}}.[[is_me]] = FALSE',
                        '{{p1}}.[[name]] IS NOT NULL',
                        '{{p1}}.[[number]] IS NOT NULL',
                    ]),
                    implode(' AND ', [
                        '{{p2}}.[[id]] IS NOT NULL',
                        '{{p2}}.[[is_me]] = FALSE',
                        '{{p2}}.[[name]] IS NOT NULL',
                        '{{p2}}.[[number]] IS NOT NULL',
                    ]),
                ]),
            ])
            ->groupBy([
                '{{%battle3}}.[[user_id]]',
                $name,
                $number,
            ]);

        if ($battle) {
            [$playerTable, $aliasTable] = match ($battle->rule_id) {
                $tricolor->id => ['{{%battle_tricolor_player3}}', '{{p2}}'],
                default => ['{{%battle_player3}}', '{{p1}}'],
            };

            $subQuery = (new Query())
                ->select(['name', 'number'])
                ->from($playerTable)
                ->andWhere([
                    '[[battle_id]]' => $battle->id,
                    '[[is_me]]' => false,
                ])
                ->andWhere(['and',
                    ['not', ['[[name]]' => null]],
                    ['not', ['[[number]]' => null]],
                ]);

            $select->innerJoin(
                ['tTargetPlayers' => $subQuery],
                implode(' AND ', [
                    "{$aliasTable}.[[name]] = {{tTargetPlayers}}.[[name]]",
                    "{$aliasTable}.[[number]] = {{tTargetPlayers}}.[[number]]",
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
