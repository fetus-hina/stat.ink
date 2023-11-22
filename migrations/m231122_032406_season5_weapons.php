<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m231122_032406_season5_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'bottlegeyser_foil',
            name: 'Foil Squeezer',
            type: 'shooter',
            sub: 'robotbomb',
            special: 'suminagasheet',
            main: 'bottlegeyser',
            salmon: false,
            aliases: ['401'],
            xGroup: 'C+',
            releaseAt: '2023-12-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'spygadget_sorella',
            name: 'Undercover Sorella Brella',
            type: 'brella',
            sub: 'torpedo',
            special: 'suminagasheet',
            main: 'spygadget',
            salmon: false,
            aliases: ['6021'],
            xGroup: 'E-',
            releaseAt: '2023-12-01T00:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('bottlegeyser_foil', salmon: false);
        $this->downWeapon3('spygadget_sorella', salmon: false);

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
