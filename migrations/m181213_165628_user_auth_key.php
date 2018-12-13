<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181213_165628_user_auth_key extends Migration
{
    public function up()
    {
        $this->createTable('user_auth_key', [
            'id' => $this->primaryKey(),
            'user_id' => $this->pkRef('user')->notNull(),
            'auth_key_hint' => $this->char(8)->notNull(),
            'auth_key_hash' => $this->string(255)->notNull(),
            'expires_at' => $this->timestampTZ(0)->notNull(),
            'created_at' => $this->timestampTZ(0)->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),
        ]);
        $this->createIndex('ix_userAuthKey_userId', 'user_auth_key', 'user_id');
        $this->createIndex('ix_userAuthKey_authKeyHint', 'user_auth_key', 'auth_key_hint');
    }

    public function down()
    {
        $this->dropTable('user_auth_key');
    }
}
