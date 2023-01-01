<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

use Yii;
use app\models\Battle2;
use app\models\Rank2;
use yii\db\Query;

use function array_filter;
use function array_keys;
use function array_map;
use function implode;
use function in_array;
use function sprintf;
use function vsprintf;

trait Weapon2Trait
{
    protected function updateEntireWeapons2(): void
    {
        $this->makeStatWeapon2Result();
        $this->makeStatWeapon2KDWinRate();
    }

    private function makeStatWeapon2Result(): void
    {
        // {{{
        $pointNormalize = 50;
        $dummyRank = Rank2::findOne(['key' => 'c-'])->id;
        $select = Battle2::find() // {{{
            ->select([
                'weapon_id' => 'battle_player2.weapon_id',
                'rule_id' => 'battle2.rule_id',
                'map_id' => 'battle2.map_id',
                'lobby_id' => 'battle2.lobby_id',
                'mode_id' => 'battle2.mode_id',
                'rank_id' => sprintf('COALESCE(battle_player2.rank_id, %d)', $dummyRank),
                'version_id' => 'battle2.version_id',
                'kill' => 'battle_player2.kill',
                'death' => 'battle_player2.death',
                'assist' => vsprintf('(%s - %s)', [
                    'battle_player2.kill_or_assist',
                    'battle_player2.kill',
                ]),
                'special' => 'battle_player2.special',
                'points' => vsprintf('(FLOOR((%s)::DOUBLE PRECISION / %2$.1f)::BIGINT * %2$d)', [
                    sprintf('(battle_player2.point - CASE %s END)', implode(' ', [
                        "WHEN rule2.key <> 'nawabari' THEN 0",
                        'WHEN battle2.is_win = battle_player2.is_my_team THEN 1000',
                        'ELSE 0',
                    ])),
                    $pointNormalize,
                ]),
                'battles' => 'COUNT(*)',
                'wins' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN battle2.is_win = battle_player2.is_my_team THEN 1',
                    'ELSE 0',
                ])),
            ])
            ->innerJoinWith([
                'battlePlayers' => function (Query $q): void {
                    $q->orderBy(null);
                },
                'lobby',
                'mode',
                'rule',
            ], false)
            ->andWhere(['and',
                ['battle2.is_automated' => true],
                ['battle2.use_for_entire' => true],
                ['not', ['battle2.is_win' => null]],
                ['not', ['battle2.start_at' => null]],
                ['not', ['battle2.end_at' => null]],
                ['battle_player2.is_me' => false],
                ['not', ['battle_player2.weapon_id' => null]],
                ['not', ['battle2.rule_id' => null]],
                ['not', ['battle2.map_id' => null]],
                ['not', ['battle2.lobby_id' => null]],
                ['not', ['battle2.mode_id' => null]],
                ['not', ['battle2.version_id' => null]],
                ['not', ['battle_player2.kill' => null]],
                ['not', ['battle_player2.death' => null]],
                ['not', ['battle_player2.kill_or_assist' => null]],
                ['not', ['battle_player2.special' => null]],
                ['not', ['battle_player2.point' => null]],
                ['<>', 'lobby2.key', 'private'],
                ['<>', 'mode2.key', 'private'],
                ['or',
                    ['and',
                        ['rule2.key' => 'nawabari'],
                        ['battle_player2.rank_id' => null],
                    ],
                    ['and',
                        ['<>', 'rule2.key', 'nawabari'],
                        ['not', ['battle_player2.rank_id' => null]],
                    ],
                ],
            ])
            ->andWhere("(battle2.end_at - battle2.start_at) >= '30 seconds'::interval")
            ->groupBy([
                'battle_player2.weapon_id',
                'battle2.rule_id',
                'battle2.map_id',
                'battle2.lobby_id',
                'battle2.mode_id',
                'battle_player2.rank_id',
                'battle2.version_id',
                'battle_player2.kill',
                'battle_player2.death',
                sprintf(
                    '(%s - %s)',
                    'battle_player2.kill_or_assist',
                    'battle_player2.kill',
                ),
                'battle_player2.special',
                sprintf(
                    '(FLOOR((%s)::DOUBLE PRECISION / %2$.1f)::BIGINT * %2$d)',
                    sprintf('(battle_player2.point - CASE %s END)', implode(' ', [
                        "WHEN rule2.key <> 'nawabari' THEN 0",
                        'WHEN battle2.is_win = battle_player2.is_my_team THEN 1000',
                        'ELSE 0',
                    ])),
                    $pointNormalize,
                ),
            ])
            ->having(['and',
                ['>',
                    sprintf(
                        '(FLOOR((%s)::DOUBLE PRECISION / %2$.1f)::BIGINT * %2$d)',
                        sprintf('(battle_player2.point - CASE %s END)', implode(' ', [
                            "WHEN rule2.key <> 'nawabari' THEN 0",
                            'WHEN battle2.is_win = battle_player2.is_my_team THEN 1000',
                            'ELSE 0',
                        ])),
                        $pointNormalize,
                    ),
                    0,
                ],
            ])
            ->orderBy(null);
        // }}}
        $insert = sprintf(
            // {{{
            'INSERT INTO stat_weapon2_result ( %s ) %s %s',
            implode(', ', array_map(fn (string $column): string => "[[{$column}]]", array_keys($select->select))),
            $select->createCommand()->rawSql,
            sprintf(
                'ON CONFLICT ( %s ) DO UPDATE SET %s',
                implode(', ', array_map(
                    fn (string $column): string => "[[{$column}]]",
                    array_filter(
                        array_keys($select->select),
                        fn (string $column): bool => !in_array($column, ['battles', 'wins'], true),
                    ),
                )),
                implode(', ', array_map(
                    fn (string $column): string => "[[{$column}]] = {{excluded}}.[[{$column}]]",
                    ['battles', 'wins'],
                )),
            ),
            // }}}
        );
        echo "Updating stat_weapon2_result...\n";
        Yii::$app->db->createCommand($insert)->execute();
        echo "Vacuum...\n";
        Yii::$app->db->createCommand('VACUUM ANALYZE stat_weapon2_result')->execute();
        // }}}
    }

    private function makeStatWeapon2KDWinRate(): void
    {
        $select = (new Query())
            ->select([
                'rule_id' => '{{src}}.[[rule_id]]',
                'map_id' => '{{src}}.[[map_id]]',
                'weapon_type_id' => '{{w}}.[[type_id]]',
                'version_group_id' => '{{ver}}.[[group_id]]',
                'rank_group_id' => '{{r}}.[[group_id]]',
                'kill' => '{{src}}.[[kill]]',
                'death' => '{{src}}.[[death]]',
                'battles' => 'SUM({{src}}.[[battles]])',
                'wins' => 'SUM({{src}}.[[wins]])',
            ])
            ->from(['{{src}}' => 'stat_weapon2_result'])
            ->innerJoin(['w' => 'weapon2'], '{{src}}.[[weapon_id]] = {{w}}.[[id]]')
            ->innerJoin(['ver' => 'splatoon_version2'], '{{src}}.[[version_id]] = {{ver}}.[[id]]')
            ->innerJoin(['r' => 'rank2'], '{{src}}.[[rank_id]] = {{r}}.[[id]]')
            ->groupBy([
                '{{src}}.[[rule_id]]',
                '{{src}}.[[map_id]]',
                '{{src}}.[[kill]]',
                '{{src}}.[[death]]',
                '{{w}}.[[type_id]]',
                '{{ver}}.[[group_id]]',
                '{{r}}.[[group_id]]',
            ]);
        $db = Yii::$app->db;
        $sql = vsprintf('INSERT INTO %s (%s) %s ON CONFLICT ON CONSTRAINT %s DO UPDATE SET %s', [
            $db->quoteTableName('stat_weapon2_kd_win_rate'),
            implode(', ', array_map(
                fn (string $cName): string => $db->quoteColumnName($cName),
                array_keys($select->select),
            )),
            $select->createCommand()->rawSql,
            $db->quoteColumnName('stat_weapon2_kd_win_rate_pkey'),
            implode(', ', array_map(
                fn (string $cName): string => vsprintf('%1$s = %2$s.%1$s', [
                        $db->quoteColumnName($cName),
                        $db->quoteTableName('excluded'),
                    ]),
                ['battles', 'wins'],
            )),
        ]);

        echo "INSERTing to stat_weapon2_kd_win_rate...\n";
        $db->createCommand($sql)->execute();

        echo "VACUUMing stat_weapon2_kd_win_rate...\n";
        $db->createCommand('VACUUM ANALYZE stat_weapon2_kd_win_rate')->execute();
    }
}
