<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230828_125729_salmon3_user_stats_golden_egg extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon3_user_stats_golden_egg}}', [
            'user_id' => $this->pkRef('user')->notNull(),
            'map_id' => $this->pkRef('{{%salmon_map3}}')->notNull(),
            'shifts' => $this->bigInteger()->notNull(),
            'average_team' => $this->double()->null(),
            'stddev_team' => $this->double()->null(),
            'histogram_width_team' => $this->integer()->null(),
            'average_individual' => $this->double()->null(),
            'stddev_individual' => $this->double()->null(),
            'histogram_width_individual' => $this->integer()->null(),

            'PRIMARY KEY ([[user_id]], [[map_id]])',
        ]);

        $this->createTable('{{%salmon3_user_stats_golden_egg_team_histogram}}', [
            'user_id' => $this->pkRef('user')->notNull(),
            'map_id' => $this->pkRef('{{%salmon_map3}}')->notNull(),
            'class_value' => $this->integer()->notNull(),
            'count' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[user_id]], [[map_id]], [[class_value]])',
        ]);

        $this->createTable('{{%salmon3_user_stats_golden_egg_individual_histogram}}', [
            'user_id' => $this->pkRef('user')->notNull(),
            'map_id' => $this->pkRef('{{%salmon_map3}}')->notNull(),
            'class_value' => $this->integer()->notNull(),
            'count' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[user_id]], [[map_id]], [[class_value]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%salmon3_user_stats_golden_egg_individual_histogram}}',
            '{{%salmon3_user_stats_golden_egg_team_histogram}}',
            '{{%salmon3_user_stats_golden_egg}}',
        ]);

        return true;
    }
}
