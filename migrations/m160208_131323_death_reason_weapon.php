<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160208_131323_death_reason_weapon extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{death_reason}} ADD COLUMN [[weapon_id]] INTEGER');
        $this->addForeignKey('fk_death_reason_2', 'death_reason', 'weapon_id', 'weapon', 'id');

        $update = 'UPDATE {{death_reason}} ';
        $update .= 'SET {{weapon_id}} = {{weapon}}.[[id]] ';
        $update .= 'FROM {{weapon}}, {{death_reason_type}} ';
        $update .= 'WHERE ( {{death_reason}}.[[key]] = {{weapon}}.[[key]] ) ';
        $update .= 'AND ( {{death_reason}}.[[type_id]] = {{death_reason_type}}.[[id]] ) ';
        $update .= 'AND ( {{death_reason_type}}.[[key]] = :mainWeapon ) ';
        $this->execute($update, [':mainWeapon' => 'main']);
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{death_reason}} DROP COLUMN [[weapon_id]]');
    }
}
