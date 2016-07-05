<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m151021_105443_user_weapon extends Migration
{
    public function up()
    {
        $this->createTable('user_weapon', [
            'user_id' => $this->integer()->notNull(),
            'weapon_id' => $this->integer()->notNull(),
            'count' => $this->bigInteger()->notNull(),
        ]);
        $this->addForeignKey('fk_user_weapon_1', 'user_weapon', 'user_id', 'user', 'id', 'CASCADE');
        $this->addForeignKey('fk_user_weapon_2', 'user_weapon', 'weapon_id', 'weapon', 'id', 'CASCADE');
        $this->addPrimaryKey('pk_user_weapon', 'user_weapon', ['user_id', 'weapon_id']);
    }

    public function down()
    {
        $this->dropTable('user_weapon');
    }
}
