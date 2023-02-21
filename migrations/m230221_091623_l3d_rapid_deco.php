<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230221_091623_l3d_rapid_deco extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'l3reelgun_d',
            name: 'L-3 Nozzlenose D',
            type: 'reelgun',
            sub: 'quickbomb',
            special: 'ultrahanko',
            main: 'l3reelgun',
            salmon: false,
            aliases: ['301'],
            xGroup: 'C-',
        );

        $this->upWeapon3(
            key: 'rapid_deco',
            name: 'Rapid Blaster Deco',
            type: 'blaster',
            sub: 'torpedo',
            special: 'jetpack',
            main: 'rapid',
            salmon: false,
            aliases: ['241'],
            xGroup: 'D+',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('l3reelgun_d', salmon: false);
        $this->downWeapon3('rapid_deco', salmon: false);

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
