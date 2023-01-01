<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m161114_123753_login_with_twitter extends Migration
{
    public function up()
    {
        $this->createTable('login_with_twitter', [
            'user_id' => 'INTEGER NOT NULL PRIMARY KEY REFERENCES {{user}}([[id]]) ON DELETE CASCADE',
            'twitter_id' => 'BIGINT NOT NULL UNIQUE', // integer id
            'screen_name' => 'VARCHAR(15) NOT NULL', // just a hint. may duplicate.
            'name' => 'VARCHAR(32) NOT NULL', // just a hint.
        ]);
    }

    public function down()
    {
        $this->dropTable('login_with_twitter');
    }
}
