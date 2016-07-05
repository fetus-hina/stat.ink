<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m151027_120258_user_env extends Migration
{
    public function up()
    {
        $this->createTable('environment', [
            'id' => $this->primaryKey(),
            'sha256sum' => 'CHAR(43) NOT NULL UNIQUE',
            'text' => 'TEXT NOT NULL',
        ]);

        $this->execute('ALTER TABLE {{user}} ADD COLUMN [[env_id]] INTEGER');
        $this->addForeignKey('fk_user_1', 'user', 'env_id', 'environment', 'id');

        $this->execute('ALTER TABLE {{battle}} ADD COLUMN [[env_id]] INTEGER');
        $this->addForeignKey('fk_battle_11', 'battle', 'env_id', 'environment', 'id');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[env_id]]');
        $this->execute('ALTER TABLE {{user}} DROP COLUMN [[env_id]]');
        $this->dropTable('environment');
    }
}
