<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221114_185407_salmon3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon3}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'uuid' => 'UUID NOT NULL UNIQUE',
            'client_uuid' => 'UUID NOT NULL',
            'is_big_run' => $this->boolean()->notNull(),
            'stage_id' => $this->pkRef('{{%salmon_map3}}')->null(),
            'big_stage_id' => $this->pkRef('{{%map3}}')->null(),
            'weapon_id' => $this->pkRef('{{%salmon_weapon3}}')->null(),
            'danger_rate' => $this->decimal(5, 1)->null(),
            'clear_waves' => $this->integer()->null(),
            'fail_reason_id' => $this->pkRef('{{%salmon_fail_reason2}}')->null(),
            'king_smell' => $this->integer()->null(),
            'king_salmonid_id' => $this->pkRef('{{%salmon_king3}}')->null(),
            'clear_extra' => $this->boolean()->null(),
            'title_before_id' => $this->pkRef('{{%salmon_title3}}')->null(),
            'title_exp_before' => $this->integer()->null(),
            'title_after_id' => $this->pkRef('{{%salmon_title3}}')->null(),
            'title_exp_after' => $this->integer()->null(),
            'golden_eggs' => $this->integer()->null(),
            'power_eggs' => $this->integer()->null(),
            'gold_scale' => $this->integer()->null(),
            'silver_scale' => $this->integer()->null(),
            'bronze_scale' => $this->integer()->null(),
            'job_point' => $this->integer()->null(),
            'job_score' => $this->integer()->null(),
            'job_rate' => $this->decimal(4, 2)->null(),
            'job_bonus' => $this->integer()->null(),
            'note' => $this->text()->null(),
            'private_note' => $this->text()->null(),
            'link_url' => 'httpurl NULL',
            'version_id' => $this->pkRef('{{%splatoon_version3}}')->null(),
            'agent_id' => $this->pkRef('{{%agent}}')->null(),
            'is_automated' => $this->boolean()->notNull(),
            'start_at' => $this->timestampTZ(0)->null(),
            'end_at' => $this->timestampTZ(0)->null(),

            'period' => $this->integer()->notNull(),
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->null(),

            'has_disconnect' => $this->boolean()->notNull()->defaultValue(false),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'remote_addr' => 'INET NOT NULL',
            'remote_port' => $this->integer()->notNull(),
            'created_at' => $this->timestampTZ(0)->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),
        ]);

        $db = $this->db;
        assert($db instanceof Connection);

        $this->execute(vsprintf('CREATE UNIQUE INDEX %s ON %s (%s) WHERE %s', [
            $db->quoteColumnName('salmon3_user_id_client_uuid'),
            $db->quoteTableName('{{%salmon3}}'),
            implode(', ', array_map(
                fn (string $column): string => $db->quoteColumnName($column),
                [
                    'user_id',
                    'client_uuid',
                ],
            )),
            sprintf('%s = FALSE', $db->quoteColumnName('is_deleted')),
        ]));

        $this->createTable('{{%salmon_agent_variable3}}', [
            'salmon_id' => $this->bigPkRef('{{%salmon3}}')->notNull(),
            'variable_id' => $this->pkRef('{{%agent_variable3}}')->notNull(),
            'PRIMARY KEY ([[salmon_id]], [[variable_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables(['{{%salmon_agent_variable}}', '{{%salmon3}}']);

        return true;
    }
}
