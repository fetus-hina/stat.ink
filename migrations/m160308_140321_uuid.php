<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160308_140321_uuid extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[client_uuid]] VARCHAR(64) NULL',
        ]));
        $this->createIndex('ix_battle_client_uuid', 'battle', ['user_id', 'client_uuid']);
    }

    public function down()
    {
        $this->dropIndex('ix_battle_client_uuid', 'battle');
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'DROP COLUMN [[client_uuid]]',
        ]));
    }
}
