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

final class m230814_072757_splatfest3_stats_power extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%splatfest3_stats_power}}', [
            'splatfest_id' => $this->pkRef('splatfest3')->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'agg_battles' => $this->bigInteger()->notNull(),
            'average' => $this->double()->null(),
            'stddev' => $this->double()->null(),
            'minimum' => $this->double()->null(),
            'p05' => $this->double()->null(),
            'p25' => $this->double()->null(),
            'p50' => $this->double()->null(),
            'p75' => $this->double()->null(),
            'p80' => $this->double()->null(),
            'p95' => $this->double()->null(),
            'maximum' => $this->double()->null(),
            'histogram_width' => $this->integer()->null(),
            'last_posted_at' => $this->timestampTZ(0)->notNull(),

            'PRIMARY KEY ([[splatfest_id]])',
        ]);

        $this->createTable('{{%splatfest3_stats_power_histogram}}', [
            'splatfest_id' => $this->pkRef('splatfest3')->notNull(),
            'class_value' => $this->integer()->notNull(),
            'battles' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[splatfest_id]], [[class_value]])',
        ]);

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $this->execute(
            vsprintf('CREATE FUNCTION %s ( %s ) %s AS $$%s$$', [
                'HISTOGRAM_WIDTH',
                implode(', ', [
                    'IN [[samples]] BIGINT',
                    'IN [[stddev]] NUMERIC',
                ]),
                implode(' ', [
                    'RETURNS INTEGER',
                    'LANGUAGE SQL',
                    'IMMUTABLE',
                    'RETURNS NULL ON NULL INPUT',
                    'SECURITY INVOKER',
                ]),
                'SELECT GREATEST(1.0, ROUND((3.5 * $2) / POWER($1, 1.0 / 3.0) / 2.0))::integer * 2',
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute(
            'DROP FUNCTION HISTOGRAM_WIDTH ( IN BIGINT, IN NUMERIC )',
        );

        $this->dropTables([
            '{{%splatfest3_stats_power_histogram}}',
            '{{%splatfest3_stats_power}}',
        ]);

        return true;
    }
}
