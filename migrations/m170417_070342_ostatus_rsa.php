<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170417_070342_ostatus_rsa extends Migration
{
    public function up()
    {
        $this->createTable('ostatus_rsa', [
            'user_id' => $this->primaryKey()->append('REFERENCES {{user}}([[id]])'),
            'bits' => $this->integer()->notNull(),
            'privkey' => $this->text()->notNull(),
            'pubkey' => $this->text()->notNull(),
            'modulus' => $this->text()->notNull(),
            'exponent' => $this->string(8)->notNull(),
            'created_at' => $this->timestampTZ()->notNull(),
            'updated_at' => $this->timestampTZ()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('ostatus_rsa');
    }
}
