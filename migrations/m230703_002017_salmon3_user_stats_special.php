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

final class m230703_002017_salmon3_user_stats_special extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon3_user_stats_special}}', array_merge(
            [
                'user_id' => $this->pkRef('{{user}}')->notNull(),
                'special_id' => $this->pkRef('{{%special3}}')->notNull(),
                'jobs' => $this->integer()->notNull(),
                'jobs_cleared' => $this->integer()->notNull(),
            ],
            $this->aggColumns('waves_cleared', $this->integer()),
            $this->aggColumns('golden_egg', $this->integer()),
            $this->aggColumns('power_egg', $this->integer()),
            $this->aggColumns('rescue', $this->integer()),
            $this->aggColumns('rescued', $this->integer()),
            $this->aggColumns('defeat_boss', $this->integer()),
            [
                'PRIMARY KEY ([[user_id]], [[special_id]])',
            ],
        ));

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $query = (new Query())
            ->select(
                array_merge(
                    [
                        'user_id' => '{{%salmon3}}.[[user_id]]',
                        'special_id' => '{{%salmon_player3}}.[[special_id]]',
                        'jobs' => 'COUNT(*)',
                        'jobs_cleared' => vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%salmon3}}.[[clear_waves]] >= 3 THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                    ],
                    self::stats(
                        'waves_cleared',
                        vsprintf('(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%salmon3}}.[[clear_waves]] >= 3 THEN 3',
                                'ELSE {{%salmon3}}.[[clear_waves]]',
                            ]),
                        ]),
                    ),
                    self::stats('golden_egg', '{{%salmon_player3}}.[[golden_eggs]]'),
                    self::stats('power_egg', '{{%salmon_player3}}.[[power_eggs]]'),
                    self::stats('rescue', '{{%salmon_player3}}.[[rescue]]'),
                    self::stats('rescued', '{{%salmon_player3}}.[[rescued]]'),
                    self::stats('defeat_boss', '{{%salmon_player3}}.[[defeat_boss]]'),
                ),
            )
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon_player3}}',
                implode(' AND ', [
                    '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                    '{{%salmon_player3}}.[[is_me]] = TRUE',
                ]),
            )
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                ],
                ['not', ['{{%salmon3}}.[[clear_waves]]' => null]],
                ['not', ['{{%salmon_player3}}.[[special_id]]' => null]],
            ])
            ->groupBy([
                '{{%salmon3}}.[[user_id]]',
                '{{%salmon_player3}}.[[special_id]]',
            ]);

        $this->execute(
            vsprintf('INSERT INTO %s (%s) %s', [
                '{{%salmon3_user_stats_special}}',
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($query->select),
                    ),
                ),
                $query->createCommand($db)->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%salmon3_user_stats_special}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon3_user_stats_special}}',
        ];
    }

    /**
     * @return array<string, ColumnSchemaBuilder>
     */
    private function aggColumns(string $baseName, ColumnSchemaBuilder $type): array
    {
        return [
            "{$baseName}_avg" => $this->double()->null(),
            "{$baseName}_sd" => $this->double()->null(),
            "{$baseName}_max" => (clone $type)->null(),
            "{$baseName}_95" => (clone $type)->null(),
            "{$baseName}_75" => (clone $type)->null(),
            "{$baseName}_50" => (clone $type)->null(),
            "{$baseName}_25" => (clone $type)->null(),
            "{$baseName}_05" => (clone $type)->null(),
            "{$baseName}_min" => (clone $type)->null(),
        ];
    }

    private static function stats(string $prefix, string $column): array
    {
        $pct = fn (float $p): string => vsprintf('PERCENTILE_DISC(%.2f) WITHIN GROUP (ORDER BY %s)', [
            $p,
            $column,
        ]);

        return [
            "{$prefix}_avg" => "AVG($column)",
            "{$prefix}_sd" => "STDDEV_POP($column)",
            "{$prefix}_max" => "MAX($column)",
            "{$prefix}_95" => $pct(0.95),
            "{$prefix}_75" => $pct(0.75),
            "{$prefix}_50" => $pct(0.50),
            "{$prefix}_25" => $pct(0.25),
            "{$prefix}_05" => $pct(0.05),
            "{$prefix}_min" => "MIN($column)",
        ];
    }
}
