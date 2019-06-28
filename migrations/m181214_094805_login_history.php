<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181214_094805_login_history extends Migration
{
    public function up()
    {
        $this->createTable('login_method', [
            'id' => $this->integer()->notNull(),
            'name' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[id]])',
        ]);
        $this->batchInsert('login_method', ['id', 'name'], [
            [ 1, 'Password' ],
            [ 2, 'Auto (cookie)' ],
            [ 3, 'Twitter' ],
        ]);
        $this->createTable('http_user_agent', [
            'id' => $this->primaryKey(),
            'user_agent' => $this->text()->notNull()->unique(),
        ]);
        $this->createTable('user_login_history', [
            'id' => $this->primaryKey(),
            'user_id' => $this->pkRef('user')->notNull(),
            'method_id' => $this->pkRef('login_method')->notNull(),
            'remote_addr' => 'INET NOT NULL',
            'remote_port' => $this->integer()->notNull(),
            'remote_host' => $this->string(255)->null(),
            'user_agent_id' => $this->pkRef('http_user_agent')->null(),
            'created_at' => $this->timestampTZ()->notNull(),
            'updated_at' => $this->timestampTZ()->notNull(),
            'UNIQUE ( [[user_id]], [[id]] )',
        ]);
    }

    public function down()
    {
        $this->dropTables([
            'user_login_history',
            'http_user_agent',
            'login_method',
        ]);
    }
}
