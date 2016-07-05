<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160207_090256_weapon_group extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{weapon}} ' . implode(', ', [
            'ADD COLUMN [[canonical_id]] INTEGER',
            'ADD COLUMN [[main_group_id]] INTEGER',
        ]));
        $this->execute('UPDATE {{weapon}} SET [[canonical_id]] = [[id]], [[main_group_id]] = [[id]]');
        $this->execute('ALTER TABLE {{weapon}} ' . implode(', ', [
            'ALTER COLUMN [[canonical_id]] SET NOT NULL',
            'ALTER COLUMN [[main_group_id]] SET NOT NULL',
        ]));
        $this->addForeignKey('fk_weapon_3', 'weapon', 'canonical_id', 'weapon', 'id');
        $this->addForeignKey('fk_weapon_4', 'weapon', 'main_group_id', 'weapon', 'id');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{weapon}} DROP COLUMN [[canonical_id]], DROP COLUMN [[main_group_id]]');
    }
}
