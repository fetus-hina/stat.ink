<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m240221_095229_season7_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: '52gal_deco',
            name: '.52 Gal Deco',
            type: 'shooter',
            sub: 'curlingbomb',
            special: 'suminagasheet',
            main: '52gal',
            salmon: false,
            aliases: ['51'],
            xGroup2: 'S',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'variableroller_foil',
            name: 'Foil Flingza Roller',
            type: 'roller',
            sub: 'kyubanbomb',
            special: 'suminagasheet',
            main: 'variableroller',
            salmon: false,
            aliases: ['1031'],
            xGroup2: 'M',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'squiclean_b',
            name: 'New Squiffer',
            type: 'charger',
            sub: 'robotbomb',
            special: 'shokuwander',
            main: 'squiclean_a',
            salmon: false,
            aliases: ['2001'],
            xGroup2: 'C',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'liter4k_custom',
            name: 'Custom E-liter 4K',
            type: 'charger',
            sub: 'jumpbeacon',
            special: 'teioika',
            main: 'liter4k',
            salmon: false,
            aliases: ['2031'],
            xGroup2: 'C',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'liter4k_scope_custom',
            name: 'Custom E-liter 4K Scope',
            type: 'charger',
            sub: 'jumpbeacon',
            special: 'teioika',
            main: 'liter4k_scope',
            salmon: false,
            aliases: ['2041'],
            xGroup2: 'C',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'explosher_custom',
            name: 'Custom Explosher',
            type: 'slosher',
            sub: 'splashshield',
            special: 'ultra_chakuchi',
            main: 'explosher',
            salmon: false,
            aliases: ['3041'],
            xGroup2: 'L',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'moprin_d',
            name: 'Dread Wringer D',
            type: 'slosher',
            sub: 'jumpbeacon',
            special: 'hopsonar',
            main: 'moprin',
            salmon: false,
            aliases: ['3051'],
            xGroup2: 'M',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'nautilus79',
            name: 'Nautilus 79',
            type: 'spinner',
            sub: 'kyubanbomb',
            special: 'ultra_chakuchi',
            main: 'nautilus47',
            salmon: false,
            aliases: ['4041'],
            xGroup2: 'M',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'kelvin525_deco',
            name: 'Glooga Dualies Deco',
            type: 'maneuver',
            sub: 'pointsensor',
            special: 'ultrashot',
            main: 'kelvin525',
            salmon: false,
            aliases: ['5021'],
            xGroup2: 'M',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'gaen_ff',
            name: 'Douser Dualies FF',
            type: 'maneuver',
            sub: 'trap',
            special: 'megaphone51',
            salmon: true,
            aliases: ['5050'],
            xGroup2: 'M',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'brella24mk1',
            name: 'Recycled Brella 24 Mk I',
            type: 'brella',
            sub: 'linemarker',
            special: 'greatbarrier',
            salmon: true,
            aliases: ['6030'],
            xGroup2: 'S',
            releaseAt: '2024-03-01T00:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $keys = [
            '52gal_deco',
            'variableroller_foil',
            'squiclean_b',
            'liter4k_custom',
            'liter4k_scope_custom',
            'explosher_custom',
            'moprin_d',
            'nautilus79',
            'kelvin525_deco',
            'gaen_ff',
            'brella24mk1',
        ];
        foreach ($keys as $key) {
            $this->downWeapon3(
                $key,
                salmon: in_array($key, ['gaen_ff', 'brella24mk1'], true),
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
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
