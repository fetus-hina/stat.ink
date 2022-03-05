<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m150922_085354_user extends Migration
{
    public function up()
    {
        $sha256HashLength = (int)ceil(256 / 8 * 4 / 3);
        $this->createTable('user', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string(15)->notNull(),
            'screen_name'   => $this->string(15)->notNull()->unique(),
            'password'      => $this->string(255)->notNull(),
            'api_key'       => $this->string($sha256HashLength)->notNull()->unique(),
            'join_at'       => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
