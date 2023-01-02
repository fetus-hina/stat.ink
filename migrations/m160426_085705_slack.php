<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160426_085705_slack extends Migration
{
    public function up()
    {
        $this->createTable('slack', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'webhook_url' => $this->string(256)->notNull(),
            'username' => $this->string(15),
            'icon' => $this->string(256),
            'channel' => $this->string(22),
            'suspended' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
            'updated_at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);
        $this->addForeignKey('fk_slack_1', 'slack', 'user_id', 'user', 'id', 'CASCADE');
        $this->addForeignKey('fk_slack_2', 'slack', 'language_id', 'language', 'id');
    }

    public function down()
    {
        $this->dropTable('slack');
    }
}
