<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m150928_133252_agent extends Migration
{
    public function up()
    {
        $this->createTable('agent', [
            'id'        => $this->primaryKey(),
            'name'      => $this->string(64)->notNull(),
            'version'   => $this->string(255)->notNull(),
        ]);
        $this->createIndex('ix_agent_1', 'agent', ['name', 'version'], true);

        // battle テーブルのデータから agent テーブルを起こす
        $select = 'SELECT {{battle}}.[[agent]], {{battle}}.[[agent_version]] ' .
            'FROM {{battle}} ' .
            'WHERE ( {{battle}}.[[agent]] IS NOT NULL ) ' .
            'GROUP BY {{battle}}.[[agent]], {{battle}}.[[agent_version]]';
        $this->execute('INSERT INTO {{agent}} ( [[name]], [[version]] ) ' . $select);

        // battle から agent への参照の追加
        $this->execute('ALTER TABLE {{battle}} ADD COLUMN [[agent_id]] INTEGER');
        $this->addForeignKey('fk_battle_6', 'battle', 'agent_id', 'agent', 'id', 'RESTRICT');

        // battle.agent,agent_version から agent_id に変換
        $this->execute(
            'UPDATE {{battle}} SET [[agent_id]] = {{t}}.[[id]] ' .
            'FROM ( SELECT * FROM {{agent}} ) AS {{t}} ' .
            'WHERE ( {{battle}}.[[agent]] = {{t}}.[[name]] ) ' .
            'AND ( {{battle}}.[[agent_version]] = {{t}}.[[version]] ) ',
        );

        $this->dropColumn('battle', 'agent');
        $this->dropColumn('battle', 'agent_version');
    }

    public function down()
    {
        echo "m150928_133252_agent cannot be reverted.\n";
        return false;
    }
}
