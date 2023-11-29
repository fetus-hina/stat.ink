<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\ColumnSchemaBuilder;
use yii\db\Query;

final class m231129_122649_stat_weapon3_x_usage extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%stat_weapon3_x_usage_term}}', [
            'id' => $this->primaryKey()->notNull(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'term' => 'TSTZRANGE NOT NULL',
            'EXCLUDE USING GIST ([[term]] WITH &&)',
        ]);

        $this->insert('{{%stat_weapon3_x_usage_term}}', [
            'key' => 'season6_spec',
            'term' => '[,)',
        ]);

        $this->createTable('{{%stat_weapon3_x_usage_range}}', [
            'id' => $this->primaryKey()->notNull(),
            'term_id' => $this->pkRef('{{%stat_weapon3_x_usage_term}}')->notNull(),
            'x_power_range' => 'NUMRANGE NOT NULL',
            // 'EXCLUDE USING GIST ([[term_id]] WITH =, [[x_power_range]] WITH &&)',
        ]);

        $this->batchInsert('{{%stat_weapon3_x_usage_range}}', ['term_id', 'x_power_range'], [
            [
                $this->key2id('{{%stat_weapon3_x_usage_term}}', 'season6_spec'),
                '[,2000.0)',
            ],
            [
                $this->key2id('{{%stat_weapon3_x_usage_term}}', 'season6_spec'),
                '[2000.0,)',
            ],
        ]);

        $this->createTable('{{%stat_weapon3_x_usage}}', array_merge(
            [
                'season_id' => $this->pkRef('{{%season3}}')->notNull(),
                'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
                'range_id' => $this->pkRef('{{%stat_weapon3_x_usage_range}}')->notNull(),
                'weapon_id' => $this->pkRef('{{%weapon3}}')->notNull(),
                'battles' => $this->bigInteger()->notNull(),
                'wins' => $this->bigInteger()->notNull(),
                'seconds' => $this->bigInteger()->notNull(),
            ],
            $this->statColumns('kill', 3),
            $this->statColumns('assist', 3),
            $this->statColumns('death', 3),
            $this->statColumns('special', 3),
            $this->statColumns('inked', 5, hasMode: false),
            [
                'PRIMARY KEY ([[season_id]], [[rule_id]], [[range_id]], [[weapon_id]])',
            ],
        ));

        $select = $this->createSelect();
        $this->execute(
            sprintf(
                'INSERT INTO %s (%s) %s',
                '{{%stat_weapon3_x_usage}}',
                implode(
                    ', ',
                    array_map(
                        fn (string $name): string => "[[{$name}]]",
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand()->rawSql,
            ),
        );

        return true;
    }

    /**
     * @return array<string, ColumnSchemaBuilder>
     */
    private function statColumns(
        string $columnName,
        int $precision1 = 3,
        int $precision2 = 1,
        bool $hasMode = true,
    ): array {
        return array_filter(
            [
                "avg_{$columnName}" => $this->double()->notNull(),
                "sd_{$columnName}" => $this->double()->null(),
                "min_{$columnName}" => $this->integer()->notNull(),
                "p05_{$columnName}" => $this->decimal($precision1, $precision2)->null(),
                "p25_{$columnName}" => $this->decimal($precision1, $precision2)->null(),
                "p50_{$columnName}" => $this->decimal($precision1, $precision2)->null(),
                "p75_{$columnName}" => $this->decimal($precision1, $precision2)->null(),
                "p95_{$columnName}" => $this->decimal($precision1, $precision2)->null(),
                "max_{$columnName}" => $this->integer()->notNull(),
                "mode_{$columnName}" => $hasMode ? $this->integer()->null() : null,
            ],
            fn (mixed $v): bool => $v !== null,
        );
    }

    private function createSelect(): Query
    {
        $stats = function (string $baseName, string $column, bool $hasMode = true): array {
            $percentile = fn (string $column, int $percentile): string => sprintf(
                'PERCENTILE_CONT(%.2f) WITHIN GROUP (ORDER BY %s)',
                $percentile / 100.0,
                $column,
            );

            $columns = [
                "avg_{$baseName}" => "AVG({$column})",
                "sd_{$baseName}" => "STDDEV_SAMP({$column})",
                "min_{$baseName}" => "MIN({$column})",
                "p05_{$baseName}" => $percentile($column, 5),
                "p25_{$baseName}" => $percentile($column, 25),
                "p50_{$baseName}" => $percentile($column, 50),
                "p75_{$baseName}" => $percentile($column, 75),
                "p95_{$baseName}" => $percentile($column, 95),
                "max_{$baseName}" => "MAX({$column})",
            ];

            return array_merge(
                $columns,
                $hasMode ? ["mode_{$baseName}" => "MODE() WITHIN GROUP (ORDER BY {$column})"] : [],
            );
        };

        $selectPkey = [
            'season_id' => '{{%season3}}.[[id]]',
            'rule_id' => '{{%battle3}}.[[rule_id]]',
            'range_id' => '{{%stat_weapon3_x_usage_range}}.[[id]]',
            'weapon_id' => '{{%battle_player3}}.[[weapon_id]]',
        ];

        return (new Query())
            ->select(array_merge(
                $selectPkey,
                [
                    'battles' => 'COUNT(*)',
                    'wins' => vsprintf('SUM(%s)', [
                        vsprintf('CASE %s END', [
                            implode(' ', [
                                'WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                    ]),
                    'seconds' => vsprintf('SUM(%s)', [
                        vsprintf('%s - %s', [
                            'EXTRACT(EPOCH FROM {{%battle3}}.[[end_at]])',
                            'EXTRACT(EPOCH FROM {{%battle3}}.[[start_at]])',
                        ]),
                    ]),
                ],
                $stats('kill', '{{%battle_player3}}.[[kill]]'),
                $stats('assist', '{{%battle_player3}}.[[assist]]'),
                $stats('death', '{{%battle_player3}}.[[death]]'),
                $stats('special', '{{%battle_player3}}.[[special]]'),
                $stats('inked', '{{%battle_player3}}.[[inked]]', false),
            ))
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->innerJoin(
                '{{%stat_weapon3_x_usage_term}}',
                '{{%battle3}}.[[start_at]] <@ {{%stat_weapon3_x_usage_term}}.[[term]]',
            )
            ->innerJoin(
                '{{%stat_weapon3_x_usage_range}}',
                implode(' AND ', [
                    '{{%stat_weapon3_x_usage_term}}.[[id]] = {{%stat_weapon3_x_usage_range}}.[[term_id]]',
                    '{{%battle3}}.[[x_power_before]] <@ {{%stat_weapon3_x_usage_range}}.[[x_power_range]]',
                ]),
            )
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $this->key2id('{{%lobby3}}', 'xmatch'),
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[end_at]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => $this->key2id('{{%rule3}}', 'tricolor')]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
                ['not', ['{{%battle3}}.[[x_power_before]]' => null]],
                ['not', ['{{%battle_player3}}.[[assist]]' => null]],
                ['not', ['{{%battle_player3}}.[[death]]' => null]],
                ['not', ['{{%battle_player3}}.[[inked]]' => null]],
                ['not', ['{{%battle_player3}}.[[kill]]' => null]],
                ['not', ['{{%battle_player3}}.[[special]]' => null]],
                ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
            ])
            ->groupBy(array_values($selectPkey));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%stat_weapon3_x_usage}}',
            '{{%stat_weapon3_x_usage_range}}',
            '{{%stat_weapon3_x_usage_term}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_weapon3_x_usage_range}}',
            '{{%stat_weapon3_x_usage_term}}',
            '{{%stat_weapon3_x_usage}}',
        ];
    }
}
