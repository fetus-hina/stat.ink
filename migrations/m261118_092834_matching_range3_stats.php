<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m261118_092834_matching_range3_stats extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        // Distribution of the team MR (average of the 4 players' Matching Range)
        $this->createTable('{{%stat_matching_range_team3}}', [
            ...$this->commonColumns(),
            'mr' => (string)$this->decimal(5, 3)->notNull()->append('CHECK ([[mr]] > 0)'),
            'teams' => (string)$this->bigInteger()->notNull()->append('CHECK ([[teams]] > 0)'),
            ...$this->timestampColumns(),
            'PRIMARY KEY ([[season_id]], [[lobby_id]], [[x_power_class]], [[rule_id]], [[mr]])',
        ]);

        // Distribution of the in-team MR spread (max - min of the 4 players' Matching Range)
        $this->createTable('{{%stat_matching_range_spread3}}', [
            ...$this->commonColumns(),
            'spread' => (string)$this->decimal(4, 1)->notNull()->append('CHECK ([[spread]] >= 0)'),
            'teams' => (string)$this->bigInteger()->notNull()->append('CHECK ([[teams]] > 0)'),
            ...$this->timestampColumns(),
            'PRIMARY KEY ([[season_id]], [[lobby_id]], [[x_power_class]], [[rule_id]], [[spread]])',
        ]);

        // |team MR difference| and wins of the higher-MR team (one row per battle)
        $this->createTable('{{%stat_matching_range_diff3}}', [
            ...$this->commonColumns(),
            'mr_diff' => (string)$this->decimal(5, 3)->notNull()->append('CHECK ([[mr_diff]] >= 0)'),
            'battles' => (string)$this->bigInteger()->notNull()->append('CHECK ([[battles]] > 0)'),
            'higher_wins' => $this->bigInteger()->null(),
            ...$this->timestampColumns(),
            'PRIMARY KEY ([[season_id]], [[lobby_id]], [[x_power_class]], [[rule_id]], [[mr_diff]])',
            'CHECK (' . implode(' OR ', [
                '([[mr_diff]] = 0 AND [[higher_wins]] IS NULL)',
                '([[mr_diff]] > 0 AND [[higher_wins]] BETWEEN 0 AND [[battles]])',
            ]) . ')',
        ]);

        // |in-team MR spread difference| and wins of the wider-spread team (one row per battle)
        $this->createTable('{{%stat_matching_range_spread_diff3}}', [
            ...$this->commonColumns(),
            'spread_diff' => (string)$this->decimal(4, 1)->notNull()->append('CHECK ([[spread_diff]] >= 0)'),
            'battles' => (string)$this->bigInteger()->notNull()->append('CHECK ([[battles]] > 0)'),
            'wider_wins' => $this->bigInteger()->null(),
            ...$this->timestampColumns(),
            'PRIMARY KEY ([[season_id]], [[lobby_id]], [[x_power_class]], [[rule_id]], [[spread_diff]])',
            'CHECK (' . implode(' OR ', [
                '([[spread_diff]] = 0 AND [[wider_wins]] IS NULL)',
                '([[spread_diff]] > 0 AND [[wider_wins]] BETWEEN 0 AND [[battles]])',
            ]) . ')',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropTables([
            '{{%stat_matching_range_spread_diff3}}',
            '{{%stat_matching_range_diff3}}',
            '{{%stat_matching_range_spread3}}',
            '{{%stat_matching_range_team3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_matching_range_team3}}',
            '{{%stat_matching_range_spread3}}',
            '{{%stat_matching_range_diff3}}',
            '{{%stat_matching_range_spread_diff3}}',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function commonColumns(): array
    {
        return [
            'season_id' => (string)$this->pkRef('{{%season3}}')->notNull(),
            'lobby_id' => (string)$this->pkRef('{{%lobby3}}')->notNull(),
            // lower bound of the uploader's X Power class; 0 for lobbies without X Power
            'x_power_class' => (string)$this->integer()->notNull()->append('CHECK ([[x_power_class]] >= 0)'),
            'rule_id' => (string)$this->pkRef('{{%rule3}}')->notNull(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function timestampColumns(): array
    {
        return [
            'created_at' => (string)$this->timestampTZ(0)->notNull(),
            'updated_at' => (string)$this->timestampTZ(0)->notNull(),
        ];
    }
}
