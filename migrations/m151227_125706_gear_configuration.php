<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151227_125706_gear_configuration extends Migration
{
    public function up()
    {
        $this->createTable('gear_configuration', [
            'id' => $this->bigPrimaryKey(),
            'finger_print' => 'CHAR(43) NOT NULL UNIQUE',
            'gear_id' => 'INTEGER NULL',
            'primary_ability_id' => 'INTEGER NULL',
        ]);
        $this->addForeignKey('fk_gear_configuration_1', 'gear_configuration', 'gear_id', 'gear', 'id');
        $this->addForeignKey('fk_gear_configuration_2', 'gear_configuration', 'primary_ability_id', 'ability', 'id');

        $this->createTable('gear_configuration_secondary', [
            'id' => $this->bigPrimaryKey(),
            'config_id' => 'BIGINT NOT NULL',
            'ability_id' => 'INTEGER NULL',
        ]);
        $this->addForeignKey(
            'fk_gear_configuration_secondary_1',
            'gear_configuration_secondary',
            'config_id',
            'gear_configuration',
            'id',
        );
        $this->addForeignKey(
            'fk_gear_configuration_secondary_2',
            'gear_configuration_secondary',
            'ability_id',
            'ability',
            'id',
        );
    }

    public function down()
    {
        $this->dropTable('gear_configuration_secondary');
        $this->dropTable('gear_configuration');
    }
}
