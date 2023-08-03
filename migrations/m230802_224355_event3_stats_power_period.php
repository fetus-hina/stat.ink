<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230802_224355_event3_stats_power_period extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%event3_stats_power_period}}', [
            'period_id' => $this->pkRef('{{%event_period3}}')->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'agg_battles' => $this->bigInteger()->notNull(),
            'average' => $this->double()->notNull(),
            'stddev' => $this->double()->null(),
            'minimum' => $this->double()->null(),
            'p05' => $this->double()->null(),
            'p25' => $this->double()->null(),
            'p50' => $this->double()->null(),
            'p75' => $this->double()->null(),
            'p80' => $this->double()->null(),
            'p95' => $this->double()->null(),
            'maximum' => $this->double()->null(),

            'PRIMARY KEY ([[period_id]])',
        ]);

        $this->createTable('{{%event3_stats_power_period_histogram}}', [
            'period_id' => $this->pkRef('{{%event_period3}}')->notNull(),
            'class_value' => $this->integer()->notNull(),
            'battles' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[period_id]], [[class_value]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%event3_stats_power_period_histogram}}',
            '{{%event3_stats_power_period}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%event3_stats_power_period}}',
            '{{%event3_stats_power_period_histogram}}',
        ];
    }
}
