<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;
use yii\db\Query;

final class m230626_060844_salmon3_played_with extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon3_user_stats_played_with}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'name' => $this->string(10)->notNull(),
            'number' => $this->string(10)->notNull(),
            'jobs' => $this->integer()->notNull(),
            'clear_jobs' => $this->integer()->notNull(),
            'clear_waves' => $this->integer()->notNull(),
            'max_danger_rate_cleared' => $this->decimal(5, 1)->null(),
            'max_danger_rate_played' => $this->decimal(5, 1)->null(),
            'team_golden_egg_avg' => $this->float()->null(),
            'team_golden_egg_sd' => $this->float()->null(),
            'team_golden_egg_max' => $this->integer()->null(),
            'team_golden_egg_95' => $this->integer()->null(),
            'team_golden_egg_75' => $this->integer()->null(),
            'team_golden_egg_50' => $this->integer()->null(),
            'team_golden_egg_25' => $this->integer()->null(),
            'team_golden_egg_05' => $this->integer()->null(),
            'team_golden_egg_min' => $this->integer()->null(),
            'golden_egg_avg' => $this->float()->null(),
            'golden_egg_sd' => $this->float()->null(),
            'golden_egg_max' => $this->integer()->null(),
            'golden_egg_95' => $this->integer()->null(),
            'golden_egg_75' => $this->integer()->null(),
            'golden_egg_50' => $this->integer()->null(),
            'golden_egg_25' => $this->integer()->null(),
            'golden_egg_05' => $this->integer()->null(),
            'golden_egg_min' => $this->integer()->null(),
            'rescue_avg' => $this->float()->null(),
            'rescue_sd' => $this->float()->null(),
            'rescue_max' => $this->integer()->null(),
            'rescue_95' => $this->integer()->null(),
            'rescue_75' => $this->integer()->null(),
            'rescue_50' => $this->integer()->null(),
            'rescue_25' => $this->integer()->null(),
            'rescue_05' => $this->integer()->null(),
            'rescue_min' => $this->integer()->null(),
            'rescued_avg' => $this->float()->null(),
            'rescued_sd' => $this->float()->null(),
            'rescued_max' => $this->integer()->null(),
            'rescued_95' => $this->integer()->null(),
            'rescued_75' => $this->integer()->null(),
            'rescued_50' => $this->integer()->null(),
            'rescued_25' => $this->integer()->null(),
            'rescued_05' => $this->integer()->null(),
            'rescued_min' => $this->integer()->null(),
            'defeat_boss_avg' => $this->float()->null(),
            'defeat_boss_sd' => $this->float()->null(),
            'defeat_boss_max' => $this->integer()->null(),
            'defeat_boss_95' => $this->integer()->null(),
            'defeat_boss_75' => $this->integer()->null(),
            'defeat_boss_50' => $this->integer()->null(),
            'defeat_boss_25' => $this->integer()->null(),
            'defeat_boss_05' => $this->integer()->null(),
            'defeat_boss_min' => $this->integer()->null(),

            'PRIMARY KEY ([[user_id]], [[name]], [[number]])',
        ]);

        $this->execute(
            vsprintf('CREATE UNIQUE INDEX %s ON %s (%s)', [
                'salmon3_user_stats_played_with_jobs',
                '{{%salmon3_user_stats_played_with}}',
                implode(', ', [
                    '[[user_id]]',
                    '[[jobs]] DESC',
                    '[[clear_jobs]] DESC',
                    '[[clear_waves]] DESC',
                    '[[max_danger_rate_cleared]] DESC',
                    '[[max_danger_rate_played]] DESC',
                    '[[name]] ASC',
                    '[[number]] ASC',
                ]),
            ]),
        );

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $query = (new Query())
            ->select(
                array_merge(
                    [
                        'user_id' => '{{%salmon3}}.[[user_id]]',
                        'name' => '{{%salmon_player3}}.[[name]]',
                        'number' => '{{%salmon_player3}}.[[number]]',
                        'jobs' => 'COUNT(*)',
                        'clear_jobs' => vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%salmon3}}.[[clear_waves]] >= 3 THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                        'clear_waves' => vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%salmon3}}.[[clear_waves]] >= 3 THEN 3',
                                'ELSE {{%salmon3}}.[[clear_waves]]',
                            ]),
                        ]),
                        'max_danger_rate_cleared' => vsprintf('MAX(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%salmon3}}.[[clear_waves]] >= 3 THEN {{%salmon3}}.[[danger_rate]]',
                                'ELSE NULL',
                            ]),
                        ]),
                        'max_danger_rate_played' => 'MAX({{%salmon3}}.[[danger_rate]])',
                    ],
                    self::stats('team_golden_egg', '{{%salmon3}}.[[golden_eggs]]'),
                    self::stats('golden_egg', '{{%salmon_player3}}.[[golden_eggs]]'),
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
                    '{{%salmon_player3}}.[[is_me]] = FALSE',
                ]),
            )
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                ],
                ['not', ['{{%salmon3}}.[[clear_waves]]' => null]],
                ['not', ['{{%salmon_player3}}.[[name]]' => null]],
                ['not', ['{{%salmon_player3}}.[[number]]' => null]],
            ])
            ->groupBy([
                '{{%salmon3}}.[[user_id]]',
                '{{%salmon_player3}}.[[name]]',
                '{{%salmon_player3}}.[[number]]',
            ]);

        $this->execute(
            vsprintf('INSERT INTO %s (%s) %s', [
                '{{%salmon3_user_stats_played_with}}',
                implode(
                    ', ',
                    array_map(
                        fn (string $column): string => $db->quoteColumnName($column), // PHP 8.1
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
            '{{%salmon3_user_stats_played_with}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon3_user_stats_played_with}}',
        ];
    }

    /**
     * @return array<string, string>
     */
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
