<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240610_100425_eggstra_histogram_renew extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%stat_eggstra_work_distrib_user_abstract3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'average' => $this->double()->null(),
            'stddev' => $this->double()->null(),
            'min' => $this->integer()->null(),
            'p05' => $this->integer()->null(),
            'p25' => $this->integer()->null(),
            'p50' => $this->integer()->null(),
            'p75' => $this->integer()->null(),
            'p80' => $this->integer()->null(),
            'p95' => $this->integer()->null(),
            'max' => $this->integer()->null(),
            'histogram_width' => $this->integer()->null(),

            'PRIMARY KEY ([[schedule_id]])',
        ]);

        $this->createTable('{{%stat_eggstra_work_distrib_user_histogram3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'class_value' => $this->integer()->notNull(),
            'count' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[schedule_id]], [[class_value]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%stat_eggstra_work_distrib_user_histogram3}}',
            '{{%stat_eggstra_work_distrib_user_abstract3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_eggstra_work_distrib_user_abstract3}}',
            '{{%stat_eggstra_work_distrib_user_histogram3}}',
        ];
    }
}
