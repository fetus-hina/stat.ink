<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m240521_092758_splatfest3_stats_weapon extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upTable();
        $this->upData();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%splatfest3_stats_weapon}}');

        return true;
    }

    private function upTable(): void
    {
        $stats = fn (string $key): array => [
            "avg_{$key}" => $this->double()->null(),
            "sd_{$key}" => $this->double()->null(),
            "min_{$key}" => $this->integer()->notNull(),
            "p05_{$key}" => $this->integer()->null(),
            "p25_{$key}" => $this->integer()->null(),
            "p50_{$key}" => $this->integer()->null(),
            "p75_{$key}" => $this->integer()->null(),
            "p95_{$key}" => $this->integer()->null(),
            "max_{$key}" => $this->integer()->notNull(),
            "mode_{$key}" => $this->integer()->null(),
        ];

        $this->createTable(
            '{{%splatfest3_stats_weapon}}',
            array_merge(
                [
                    'fest_id' => $this->pkRef('{{%splatfest3}}')->notNull(),
                    'lobby_id' => $this->pkRef('{{%lobby3}}')->notNull(),
                    'weapon_id' => $this->pkRef('{{%weapon3}}')->notNull(),
                    'battles' => $this->bigInteger()->notNull(),
                    'wins' => $this->bigInteger()->notNull(),
                ],
                $stats('kill'),
                $stats('assist'),
                $stats('death'),
                $stats('special'),
                $stats('inked'),
                ['PRIMARY KEY ([[fest_id]], [[lobby_id]], [[weapon_id]])'],
            ),
        );
    }

    private function upData(): void
    {
        $stats = fn (string $key): array => [
            "avg_{$key}" => "AVG({{battle_player3}}.[[{$key}]])",
            "sd_{$key}" => "STDDEV_SAMP({{battle_player3}}.[[{$key}]])",
            "min_{$key}" => "MIN({{battle_player3}}.[[{$key}]])",
            "p05_{$key}" => "PERCENTILE_CONT(0.05) WITHIN GROUP (ORDER BY {{battle_player3}}.[[{$key}]])",
            "p25_{$key}" => "PERCENTILE_CONT(0.25) WITHIN GROUP (ORDER BY {{battle_player3}}.[[{$key}]])",
            "p50_{$key}" => "PERCENTILE_CONT(0.50) WITHIN GROUP (ORDER BY {{battle_player3}}.[[{$key}]])",
            "p75_{$key}" => "PERCENTILE_CONT(0.75) WITHIN GROUP (ORDER BY {{battle_player3}}.[[{$key}]])",
            "p95_{$key}" => "PERCENTILE_CONT(0.95) WITHIN GROUP (ORDER BY {{battle_player3}}.[[{$key}]])",
            "max_{$key}" => "MAX({{battle_player3}}.[[{$key}]])",
            "mode_{$key}" => "MODE() WITHIN GROUP (ORDER BY {{battle_player3}}.[[{$key}]])",
        ];

        $query = (new Query())
            ->select(
                array_merge(
                    [
                        'fest_id' => '{{%splatfest3}}.[[id]]',
                        'lobby_id' => '{{%battle3}}.[[lobby_id]]',
                        'weapon_id' => '{{%battle_player3}}.[[weapon_id]]',
                        'battles' => 'COUNT(*)',
                        'wins' => vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                    ],
                    $stats('kill'),
                    $stats('assist'),
                    $stats('death'),
                    $stats('special'),
                    $stats('inked'),
                ),
            )
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin(
                '{{%splatfest3}}',
                implode(' AND ', [
                    '{{%battle3}}.[[start_at]] >= {{%splatfest3}}.[[start_at]]',
                    '{{%battle3}}.[[start_at]] < {{%splatfest3}}.[[end_at]]',
                ]),
            )
            ->innerJoin(
                '{{%battle_player3}}',
                '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]',
            )
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => [
                        $this->key2id('{{%lobby3}}', 'splatfest_challenge'),
                        $this->key2id('{{%lobby3}}', 'splatfest_open'),
                    ],
                    '{{%battle3}}.[[rule_id]]' => $this->key2id('{{%rule3}}', 'nawabari'),
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle_player3}}.[[assist]]' => null]],
                ['not', ['{{%battle_player3}}.[[death]]' => null]],
                ['not', ['{{%battle_player3}}.[[inked]]' => null]],
                ['not', ['{{%battle_player3}}.[[kill]]' => null]],
                ['not', ['{{%battle_player3}}.[[special]]' => null]],
                ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
            ])
            ->groupBy([
                '{{%splatfest3}}.[[id]]',
                '{{%battle3}}.[[lobby_id]]',
                '{{%battle_player3}}.[[weapon_id]]',
            ]);

        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $this->db->quoteTableName('{{%splatfest3_stats_weapon}}'),
            implode(
                ', ',
                array_map(
                    $this->db->quoteColumnName(...),
                    array_keys($query->select),
                ),
            ),
            $query->createCommand()->rawSql,
        ]);

        $this->execute($sql);
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%splatfest3_stats_weapon}}',
        ];
    }
}
