<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m230121_181818_stat_weapon3_usage_per_version extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->createTable('{{%stat_weapon3_usage_per_version}}', array_merge(
            [
                'version_id' => $this->pkRef('{{%splatoon_version3}}')->notNull(),
                'lobby_id' => $this->pkRef('{{%lobby3}}')->notNull(),
                'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
                'weapon_id' => $this->pkRef('{{%weapon3}}')->notNull(),
                'battles' => $this->bigInteger()->notNull(),
                'wins' => $this->bigInteger()->notNull(),
                'seconds' => $this->bigInteger()->notNull(),
            ],
            $this->metrics('kill'),
            $this->metrics('assist'),
            $this->metrics('death'),
            $this->metrics('special'),
            $this->metrics('inked', 4, 1, false),
            ['PRIMARY KEY ([[version_id]], [[lobby_id]], [[rule_id]], [[weapon_id]])'],
        ));

        $select = $this->buildSelect($db);
        $this->execute(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%stat_weapon3_usage_per_version}}'),
                implode(
                    ', ',
                    array_map(
                        fn (string $name): string => $db->quoteColumnName($name),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_weapon3_usage_per_version}}');

        return true;
    }

    private function metrics(string $baseName, int $intPartSize = 2, int $decPartSize = 1, bool $mode = true): array
    {
        $size1 = $intPartSize + $decPartSize;
        $size2 = $decPartSize;

        $columns = [
            "avg_{$baseName}" => $this->double()->notNull(),
            "sd_{$baseName}" => $this->double()->null(),
            "min_{$baseName}" => $this->integer()->notNull(),
            "p05_{$baseName}" => $this->decimal($size1, $size2)->null(),
            "p25_{$baseName}" => $this->decimal($size1, $size2)->null(),
            "p50_{$baseName}" => $this->decimal($size1, $size2)->null(),
            "p75_{$baseName}" => $this->decimal($size1, $size2)->null(),
            "p95_{$baseName}" => $this->decimal($size1, $size2)->null(),
            "max_{$baseName}" => $this->integer()->notNull(),
        ];

        return $mode
            ? array_merge($columns, ["mode_{$baseName}" => $this->integer()->null()])
            : $columns;
    }

    private function buildSelect(Connection $db): Query
    {
        $lobbies = ArrayHelper::map(
            (new Query())->select('*')->from('{{%lobby3}}')->all($db),
            'key',
            'id',
        );

        $selectPkey = [
            'version_id' => '{{%splatoon_version3}}.[[id]]',
            'lobby_id' => vsprintf('(CASE %s END)', [
                implode(' ', [
                    vsprintf('WHEN {{%%battle3}}.[[lobby_id]] = %d THEN %d', [
                        $lobbies['splatfest_challenge'],
                        $lobbies['regular'],
                    ]),
                    'ELSE {{%battle3}}.[[lobby_id]]',
                ]),
            ]),
            'rule_id' => '{{%battle3}}.[[rule_id]]',
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
                $this->selectMetrics('kill', '{{%battle_player3}}.[[kill]]'),
                $this->selectMetrics('assist', '{{%battle_player3}}.[[assist]]'),
                $this->selectMetrics('death', '{{%battle_player3}}.[[death]]'),
                $this->selectMetrics('special', '{{%battle_player3}}.[[special]]'),
                $this->selectMetrics('inked', '{{%battle_player3}}.[[inked]]', false),
            ))
            ->from('{{%battle3}}')
            ->innerJoin('{{%splatoon_version3}}', '{{%battle3}}.[[version_id]] = {{%splatoon_version3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => [
                        $lobbies['bankara_challenge'],
                        $lobbies['regular'],
                        $lobbies['splatfest_challenge'],
                        $lobbies['xmatch'],
                    ],
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[end_at]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => $this->key2id('{{%rule3}}', 'tricolor')]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
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

    private function selectMetrics(string $baseName, string $column, bool $mode = true): array
    {
        $columns = [
            "avg_{$baseName}" => "AVG({$column})",
            "sd_{$baseName}" => "STDDEV_SAMP({$column})",
            "min_{$baseName}" => "MIN({$column})",
            "p05_{$baseName}" => $this->percentile($column, 5),
            "p25_{$baseName}" => $this->percentile($column, 25),
            "p50_{$baseName}" => $this->percentile($column, 50),
            "p75_{$baseName}" => $this->percentile($column, 75),
            "p95_{$baseName}" => $this->percentile($column, 95),
            "max_{$baseName}" => "MAX({$column})",
        ];

        return $mode
            ? array_merge($columns, ["mode_{$baseName}" => "MODE() WITHIN GROUP (ORDER BY {$column})"])
            : $columns;
    }

    private function percentile(string $column, int $percentile): string
    {
        return vsprintf('PERCENTILE_CONT(%.2f) WITHIN GROUP (ORDER BY %s)', [
            $percentile / 100.0,
            $column,
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_weapon3_usage_per_version}}',
        ];
    }
}
