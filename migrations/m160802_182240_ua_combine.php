<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160802_182240_ua_combine extends Migration
{
    public function up()
    {
        $this->createTable('agent_group', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull(),
        ]);

        $this->createTable('agent_group_map', [
            'group_id' => $this->integer()->notNull(),
            'agent_name' => $this->string(64)->notNull(),
        ]);
        $this->addPrimaryKey('pk_agent_group_map', 'agent_group_map', ['group_id', 'agent_name']);
        $this->addForeignKey('fk_agent_group_map_1', 'agent_group_map', 'group_id', 'agent_group', 'id');
    }

    public function down()
    {
        $this->dropTable('agent_group_map');
        $this->dropTable('agent_group');
    }
}
