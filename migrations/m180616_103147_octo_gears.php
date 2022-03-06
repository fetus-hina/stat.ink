<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m180616_103147_octo_gears extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $abilities = $this->abilities();

        foreach ($this->updateList() as $name => $data) {
            $key = static::name2key($name);
            $update = [
                'ability_id' => $data['ability'] === null
                    ? null
                    : $abilities[static::name2key($data['ability'])],
                'splatnet' => (int)$data['newid'],
            ];
            $this->update('gear2', $update, ['key' => $key]);
        }
    }

    public function safeDown()
    {
        foreach ($this->updateList() as $name => $data) {
            $key = static::name2key($name);
            $update = [
                'ability_id' => null,
                'splatnet' => (int)$data['oldid'],
            ];
            $this->update('gear2', $update, ['key' => $key]);
        }
    }

    public function updateList()
    {
        return [
            // headgear
            'Octoling Shades' => [
                'oldid' => 27104,
                'newid' => 27104,
                'ability' => 'Last-Ditch Effort',
            ],
            'Null Visor Replica' => [
                'oldid' => 27105,
                'newid' => 27105,
                'ability' => 'Special Power Up',
            ],
            'Old-Timey Hat' => [
                'oldid' => 27106,
                'newid' => 27106,
                'ability' => 'Comeback',
            ],
            'Conductor Cap' => [
                'oldid' => 27107,
                'newid' => 27107,
                'ability' => 'Sub Power Up',
            ],
            'Golden Toothpick' => [
                'oldid' => 27108,
                'newid' => 27108,
                'ability' => 'Special Charge Up',
            ],

            // clothing
            'Neo Octoling Armor' => [
                'oldid' => 27104,
                'newid' => 27104,
                'ability' => 'Haunt',
            ],
            'Null Armor Replica' => [
                'oldid' => 27105,
                'newid' => 27105,
                'ability' => 'Ink Resistance Up',
            ],
            'Octoleet Armor' => [
                'oldid' => 27106,
                'newid' => 21006,
                'ability' => null,
            ],

            // shoes
            'Octoleet Boots' => [
                'oldid' => 21003,
                'newid' => 21003,
                'ability' => null,
            ],
            'Neo Octoling Boots' => [
                'oldid' => 27104,
                'newid' => 27104,
                'ability' => 'Object Shredder',
            ],
            'Null Boots Replica' => [
                'oldid' => 27105,
                'newid' => 27105,
                'ability' => 'Drop Roller',
            ],
            'Old-Timey Shoes' => [
                'oldid' => 27106,
                'newid' => 27106,
                'ability' => 'Thermal Ink',
            ],
        ];
    }

    public function abilities()
    {
        $query = (new Query())
            ->select(['id', 'key'])
            ->from('ability2');
        return ArrayHelper::map(
            $query->all(),
            'key',
            'id'
        );
    }
}
