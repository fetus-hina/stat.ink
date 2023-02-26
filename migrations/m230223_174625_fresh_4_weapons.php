<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230223_174625_fresh_4_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'sharp_neo',
            name: 'Neo Splash-o-matic',
            type: 'shooter',
            sub: 'kyubanbomb',
            special: 'tripletornado',
            main: 'sharp',
            salmon: false,
            aliases: ['21'],
            xGroup: 'C-',
        );
        $this->upWeapon3(
            key: 'jetsweeper_custom',
            name: 'Custom Jet Squelcher',
            type: 'shooter',
            sub: 'poisonmist',
            special: 'amefurashi',
            main: 'jetsweeper',
            salmon: false,
            aliases: ['91'],
            xGroup: 'C+',
        );
        $this->upWeapon3(
            key: 'hissen_hue',
            name: 'Tri-Slosher Nouveau',
            type: 'slosher',
            sub: 'tansanbomb',
            special: 'energystand',
            main: 'hissen',
            salmon: false,
            aliases: ['3011'],
            xGroup: 'D-',
        );
        $this->upWeapon3(
            key: 'bold_neo',
            name: 'Neo Sploosh-o-matic',
            type: 'shooter',
            sub: 'jumpbeacon',
            special: 'megaphone51',
            main: 'bold',
            salmon: false,
            aliases: ['1'],
            xGroup: 'C-',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('sharp_neo', salmon: false);
        $this->downWeapon3('jetsweeper_custom', salmon: false);
        $this->downWeapon3('hissen_hue', salmon: false);
        $this->downWeapon3('bold_neo', salmon: false);

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
