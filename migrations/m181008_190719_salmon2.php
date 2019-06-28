<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181008_190719_salmon2 extends Migration
{
    public function up()
    {
        $this->createTable('salmon_fail_reason2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(64)->notNull(),
        ]);
        $this->batchInsert('salmon_fail_reason2', ['key', 'name'], [
            ['annihilated', 'Dead all players'],
            ['time_up', 'Time was up'],
        ]);
        $this->createTable('salmon2', [
            'id' => $this->primaryKey(),
            'user_id' => $this->pkRef('user')->notNull(),
            'uuid' => 'UUID NOT NULL',
            'splatnet_number' => $this->integer()->null(),
            'stage_id' => $this->pkRef('salmon_map2')->null(),
            'clear_waves' => $this->integer()->null(),
            'fail_reason_id' => $this->pkRef('salmon_fail_reason2')->null(),
            'title_before_id' => $this->pkRef('salmon_title2')->null(),
            'title_before_exp' => $this->integer()->null(),
            'title_after_id' => $this->pkRef('salmon_title2')->null(),
            'title_after_exp' => $this->integer()->null(),
            'danger_rate' => $this->decimal(4, 1)->null(),
            'shift_period' => $this->integer()->null(),
            'start_at' => $this->timestampTZ()->null(),
            'end_at' => $this->timestampTZ()->null(),
            'note' => $this->text()->null(),
            'private_note' => $this->text()->null(),
            'link_url' => 'HTTPURL NULL',
            'is_automated' => $this->boolean()->null(),
            'agent_id' => $this->pkRef('agent')->null(),
            'remote_addr' => 'INET NULL',
            'remote_port' => $this->integer()->null(),
            'created_at' => $this->timestampTZ()->notNull(),
            'updated_at' => $this->timestampTZ()->notNull(),
        ]);
        $this->createTable('salmon_boss_appearance2', [
            'salmon_id' => $this->pkRef('salmon2')->notNull(),
            'boss_id' => $this->pkRef('salmon_boss2')->notNull(),
            'count' => $this->integer()->notNull(),
            'PRIMARY KEY ([[salmon_id]], [[boss_id]])',
        ]);
        $this->createTable('salmon_wave2', [
            'salmon_id' => $this->pkRef('salmon2')->notNull(),
            'wave' => $this->integer()->notNull(),
            'event_id' => $this->pkRef('salmon_event2')->null(),
            'water_id' => $this->pkRef('salmon_water_level2')->null(),
            'golden_egg_quota' => $this->integer()->null(),
            'golden_egg_appearances' => $this->integer()->null(),
            'golden_egg_delivered' => $this->integer()->null(),
            'power_egg_collected' => $this->integer()->null(),
            'PRIMARY KEY ([[salmon_id]], [[wave]])',
        ]);
    }

    public function down()
    {
        $tables = [
            'salmon_wave2',
            'salmon_boss_appearance2',
            'salmon2',
            'salmon_fail_reason2',
        ];
        foreach ($tables as $table) {
            $this->execute("DROP TABLE IF EXISTS {$table}");
            // $this->dropTable($table);
        }
    }
}
