<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230217_134014_kraken_weapons extends Migration
{
    use AutoKey;
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: '96gal_deco',
            name: '.96 Gal Deco',
            type: 'shooter',
            sub: 'splashshield',
            special: 'teioika',
            main: '96gal',
            salmon: false,
            aliases: [
                self::name2key3('.96 Gal Deco'),
                '81',
            ],
            xGroup: 'C+',
        );

        $this->upWeapon3(
            key: 'splatroller_collabo',
            name: 'Krak-On Splat Roller',
            type: 'roller',
            sub: 'jumpbeacon',
            special: 'teioika',
            main: 'splatroller',
            salmon: false,
            aliases: [
                self::name2key3('Krak-On Splat Roller'),
                '1011',
            ],
            xGroup: 'E-',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('96gal_deco', salmon: false);
        $this->downWeapon3('splatroller_collabo', salmon: false);

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
