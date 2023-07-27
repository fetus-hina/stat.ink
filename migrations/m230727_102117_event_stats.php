<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\ColumnSchemaBuilder;
use yii\db\Connection;
use yii\db\Query;

final class m230727_102117_event_stats extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        return $this->upTables() && $this->initWeaponStats() && $this->initSpecialStats();
    }

    private function upTables(): bool
    {
        return $this->upTable('weapon', '{{%weapon3}}') &&
            $this->upTable('special', '{{%special3}}');
    }

    private function upTable(string $name, string $tableName): bool
    {
        $this->createTable("{{%event3_stats_{$name}}}", array_merge(
            [
                'schedule_id' => $this->pkRef('{{%event_schedule3}}')->notNull(),
                "{$name}_id" => $this->pkRef($tableName)->notNull(),
                'battles' => $this->bigInteger()->notNull(),
                'wins' => $this->bigInteger()->notNull(),
            ],
            $this->statsColumnDefs('kill'),
            $this->statsColumnDefs('assist'),
            $this->statsColumnDefs('death'),
            $this->statsColumnDefs('special'),
            $this->statsColumnDefs('inked', false),
            [
                "PRIMARY KEY ([[schedule_id]], [[{$name}_id]])",
            ],
        ));

        $this->createIndex(
            "event3_stats_{$name}_battles",
            "{{%event3_stats_{$name}}}",
            ['schedule_id', 'battles', 'wins', "{$name}_id"],
            true,
        );

        return true;
    }

    private function initWeaponStats(): bool
    {
        return $this->initStats(
            '{{%event3_stats_weapon}}',
            '[[weapon_id]]',
            '{{%weapon3}}.[[id]]',
        );
    }

    private function initSpecialStats(): bool
    {
        return $this->initStats(
            '{{%event3_stats_special}}',
            '[[special_id]]',
            '{{%weapon3}}.[[special_id]]',
        );
    }

    private function initStats(
        string $dstTableName,
        string $dstColumnName,
        string $groupColumnName,
    ): bool {
        $select = (new Query())
            ->select(
                array_merge(
                    [
                        'schedule_id' => '{{%event_schedule3}}.[[id]]',
                        $dstColumnName => $groupColumnName,
                        'battles' => 'COUNT(*)',
                        'wins' => vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%battle_player3}}.[[is_our_team]] = {{%result3}}.[[is_win]] THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                    ],
                    self::statsColumns('kill', '{{%battle_player3}}.[[kill]]'),
                    self::statsColumns('assist', '{{%battle_player3}}.[[assist]]'),
                    self::statsColumns('death', '{{%battle_player3}}.[[death]]'),
                    self::statsColumns('special', '{{%battle_player3}}.[[special]]'),
                    self::statsColumns('inked', '{{%battle_player3}}.[[inked]]', false),
                ),
            )
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->innerJoin('{{%weapon3}}', '{{%battle_player3}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
            ->innerJoin('{{%event_schedule3}}', implode(' AND ', [
                '{{%battle3}}.[[event_id]] = {{%event_schedule3}}.[[event_id]]',
                vsprintf('(%s BETWEEN %s AND %s)', [
                    '{{%battle3}}.[[start_at]]',
                    '{{%event_schedule3}}.[[start_at]]',
                    "{{%event_schedule3}}.[[end_at]] + '10 minutes'::interval",
                ]),
            ]))
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $this->key2id('{{%lobby3}}', 'event'),
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[event_id]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%battle_player3}}.[[assist]]' => null]],
                ['not', ['{{%battle_player3}}.[[death]]' => null]],
                ['not', ['{{%battle_player3}}.[[kill]]' => null]],
                ['not', ['{{%battle_player3}}.[[special]]' => null]],
                ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
                ['not', ['{{%event_schedule3}}.[[id]]' => null]],
            ])
            ->groupBy([
                '{{%event_schedule3}}.[[id]]',
                $groupColumnName,
            ]);

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName($dstTableName),
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand()->rawSql,
        ]);
        $this->execute($sql);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%event3_stats_weapon}}',
            '{{%event3_stats_special}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%event3_stats_weapon}}',
            '{{%event3_stats_special}}',
        ];
    }

    /**
     * @return array<string, string|ColumnSchemaBuilder>
     */
    private function statsColumnDefs(string $name, bool $mode = true): array
    {
        return array_filter(
            [
                "avg_{$name}" => $this->double()->null(),
                "sd_{$name}" => $this->double()->null(),
                "min_{$name}" => $this->integer()->notNull(),
                "p05_{$name}" => $this->integer()->null(),
                "p25_{$name}" => $this->integer()->null(),
                "p50_{$name}" => $this->integer()->null(),
                "p75_{$name}" => $this->integer()->null(),
                "p95_{$name}" => $this->integer()->null(),
                "max_{$name}" => $this->integer()->notNull(),
                "mode_{$name}" => $mode ? $this->integer()->null() : null,
            ],
            fn (string|ColumnSchemaBuilder|null $v): bool => $v !== null,
        );
    }

    /**
     * @return array<string, string>
     */
    private static function statsColumns(string $name, string $srcColumn, bool $mode = true): array
    {
        return array_filter(
            [
                "avg_{$name}" => "AVG($srcColumn)",
                "sd_{$name}" => "STDDEV_SAMP($srcColumn)",
                "min_{$name}" => "MIN($srcColumn)",
                "p05_{$name}" => "PERCENTILE_DISC(0.05) WITHIN GROUP (ORDER BY $srcColumn)",
                "p25_{$name}" => "PERCENTILE_DISC(0.25) WITHIN GROUP (ORDER BY $srcColumn)",
                "p50_{$name}" => "PERCENTILE_DISC(0.50) WITHIN GROUP (ORDER BY $srcColumn)",
                "p75_{$name}" => "PERCENTILE_DISC(0.75) WITHIN GROUP (ORDER BY $srcColumn)",
                "p95_{$name}" => "PERCENTILE_DISC(0.95) WITHIN GROUP (ORDER BY $srcColumn)",
                "max_{$name}" => "MAX($srcColumn)",
                "mode_{$name}" => $mode ? "MODE() WITHIN GROUP (ORDER BY $srcColumn)" : null,
            ],
            fn (?string $v): bool => $v !== null,
        );
    }
}
