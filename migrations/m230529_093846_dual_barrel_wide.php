<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230529_093846_dual_barrel_wide extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'dualsweeper_custom',
            name: 'Custom Dualie Squelchers',
            type: 'maneuver',
            sub: 'jumpbeacon',
            special: 'decoy',
            main: 'dualsweeper',
            salmon: false,
            aliases: ['5031'],
            xGroup: 'C+',
            releaseAt: '2023-06-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'barrelspinner_deco',
            name: 'Heavy Splatling Deco',
            type: 'spinner',
            sub: 'pointsensor',
            special: 'teioika',
            main: 'barrelspinner',
            salmon: false,
            aliases: ['4011'],
            xGroup: 'B',
            releaseAt: '2023-06-01T00:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'wideroller_collabo',
            name: 'Big Swig Roller Express',
            type: 'roller',
            sub: 'linemarker',
            special: 'amefurashi',
            main: 'wideroller',
            salmon: false,
            aliases: ['1041'],
            xGroup: 'E+',
            releaseAt: '2023-06-01T00:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('dualsweeper_custom', salmon: false);
        $this->downWeapon3('barrelspinner_deco', salmon: false);
        $this->downWeapon3('wideroller_collabo', salmon: false);

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
