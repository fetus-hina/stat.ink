<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m161110_120346_user_icon extends Migration
{
    public function up()
    {
        $this->createTable('user_icon', [
            'user_id' => 'INTEGER NOT NULL PRIMARY KEY REFERENCES {{user}}([[id]]) ON DELETE CASCADE',
            'filename' => $this->string(64)->notNull()->unique(),
        ]);
    }

    public function down()
    {
        $this->dropTable('user_icon');
    }
}
