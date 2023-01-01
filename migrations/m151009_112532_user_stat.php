<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151009_112532_user_stat extends Migration
{
    public function up()
    {
        $this->createTable('user_stat', [
            'user_id' => 'INTEGER NOT NULL PRIMARY KEY',
            'battle_count' => $this->bigInteger()->notNull()->defaultValue(0),
            'wp' => $this->decimal(4, 1),
            'wp_short' => $this->decimal(4, 1),
            'total_kill' => $this->bigInteger()->notNull()->defaultValue(0),
            'total_death' => $this->bigInteger()->notNull()->defaultValue(0),
            'nawabari_count' => $this->bigInteger()->notNull()->defaultValue(0),
            'nawabari_wp' => $this->decimal(4, 1),
            'nawabari_kill' => $this->bigInteger()->notNull()->defaultValue(0),
            'nawabari_death' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_count' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_wp' => $this->decimal(4, 1),
            'gachi_kill' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_death' => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('fk_user_stat_1', 'user_stat', 'user_id', 'user', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('user_stat');
    }
}
