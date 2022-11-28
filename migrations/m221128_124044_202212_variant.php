<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m221128_124044_202212_variant extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'momiji',
            name: 'Custom Splattershot Jr.',
            type: 'shooter',
            sub: 'torpedo',
            special: 'hopsonar',
            main: 'wakaba',
            salmon: false,
            aliases: ['11'],
        );

        $this->upWeapon3(
            key: 'carbon_deco',
            name: 'Carbon Roller Deco',
            type: 'roller',
            sub: 'quickbomb',
            special: 'ultrashot',
            main: 'carbon',
            salmon: false,
            aliases: ['1001'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('momiji', salmon: false);
        $this->downWeapon3('carbon_deco', salmon: false);

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%mainweapon3}}',
            '{{%weapon3}}',
            '{{%weapon3_alias}}',
            '{{%salmon_weapon3}}',
            '{{%salmon_weapon3_alias}}',
        ];
    }
}
