<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230823_112003_add_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'examiner',
            name: 'Heavy Edit Splatling',
            type: 'spinner',
            sub: 'curlingbomb',
            special: 'energystand',
            salmon: true,
            aliases: ['4050'],
            xGroup: 'C+',
            releaseAt: '2023-09-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'hokusai_hue',
            name: 'Octobrush Nouveau',
            type: 'brush',
            sub: 'jumpbeacon',
            special: 'amefurashi',
            main: 'hokusai',
            salmon: false,
            aliases: ['1111'],
            xGroup: 'E-',
            releaseAt: '2023-09-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'soytuber_custom',
            name: 'Custom Goo Tuber',
            type: 'charger',
            sub: 'tansanbomb',
            special: 'ultrahanko',
            main: 'soytuber',
            salmon: false,
            aliases: ['2061'],
            xGroup: 'A-',
            releaseAt: '2023-09-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'parashelter_sorella',
            name: 'Sorella Brella',
            type: 'brella',
            sub: 'robotbomb',
            special: 'jetpack',
            main: 'parashelter',
            salmon: false,
            aliases: ['6001'],
            xGroup: 'E-',
            releaseAt: '2023-09-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'furo_deco',
            name: 'Bloblobber Deco',
            type: 'slosher',
            sub: 'linemarker',
            special: 'teioika',
            main: 'furo',
            salmon: false,
            aliases: ['3031'],
            xGroup: 'D+',
            releaseAt: '2023-09-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'kugelschreiber_hue',
            name: 'Ballpoint Splatling Nouveau',
            type: 'spinner',
            sub: 'trap',
            special: 'kyuinki',
            salmon: false,
            aliases: ['4031'],
            xGroup: 'B',
            releaseAt: '2023-09-01T00:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('examiner', salmon: true);
        $this->downWeapon3('hokusai_hue', salmon: false);
        $this->downWeapon3('soytuber_custom', salmon: false);
        $this->downWeapon3('parashelter_sorella', salmon: false);
        $this->downWeapon3('furo_deco', salmon: false);
        $this->downWeapon3('kugelschreiber_hue', salmon: false);

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
