<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171112_165011_gear_configuration2 extends Migration
{
    public function up()
    {
        $this->createTable('gear_configuration2', [
            'id'                    => $this->primaryKey(),
            'finger_print'          => $this->char(43)->notNull()->unique(),
            'gear_id'               => $this->pkRef('gear2')->null(),
            'primary_ability_id'    => $this->pkRef('ability2')->null(),
        ]);
        $this->createTable('gear_configuration_secondary2', [
            'id'            => $this->primaryKey(),
            'config_id'     => $this->pkRef('gear_configuration2'),
            'ability_id'    => $this->pkRef('ability2')->null(),
        ]);
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', array_map(
            function (string $column): string {
                return sprintf(
                    'ADD COLUMN [[%s]] INTEGER NULL REFERENCES {{%s}}([[%s]])',
                    $column,
                    'gear_configuration2',
                    'id',
                );
            },
            ['headgear_id', 'clothing_id', 'shoes_id'],
        )));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', array_map(
            function (string $column): string {
                return sprintf('DROP COLUMN [[%s]]', $column);
            },
            ['headgear_id', 'clothing_id', 'shoes_id'],
        )));
        $this->dropTable('gear_configuration_secondary2');
        $this->dropTable('gear_configuration2');
    }
}
