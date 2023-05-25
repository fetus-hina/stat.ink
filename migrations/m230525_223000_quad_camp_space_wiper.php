<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230525_223000_quad_camp_space_wiper extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'quadhopper_white',
            name: 'Light Tetra Dualies',
            type: 'maneuver',
            sub: 'sprinkler',
            special: 'shokuwander',
            main: 'quadhopper_black',
            salmon: false,
            aliases: ['5041'],
            xGroup: 'C-',
            releaseAt: '2023-06-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'campingshelter_sorella',
            name: 'Tenta Sorella Brella',
            type: 'brella',
            sub: 'trap',
            special: 'ultrashot',
            main: 'campingshelter',
            salmon: false,
            aliases: ['6011'],
            xGroup: 'E+',
            releaseAt: '2023-06-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'spaceshooter_collabo',
            name: 'Annaki Splattershot Nova',
            type: 'shooter',
            sub: 'trap',
            special: 'jetpack',
            main: 'spaceshooter',
            salmon: false,
            aliases: ['101'],
            xGroup: 'C+',
            releaseAt: '2023-06-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'drivewiper_deco',
            name: 'Splatana Wiper Deco',
            type: 'wiper',
            sub: 'jumpbeacon',
            special: 'missile',
            main: 'drivewiper',
            salmon: false,
            aliases: ['8011'],
            xGroup: 'E-',
            releaseAt: '2023-06-01T00:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('quadhopper_white', salmon: false);
        $this->downWeapon3('campingshelter_sorella', salmon: false);
        $this->downWeapon3('spaceshooter_collabo', salmon: false);
        $this->downWeapon3('drivewiper_deco', salmon: false);

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
