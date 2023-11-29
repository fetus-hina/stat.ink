<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m231129_033200_season6_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'maneuver_collabo',
            name: 'Enperry Splat Dualies',
            type: 'maneuver',
            sub: 'curlingbomb',
            special: 'ultra_chakuchi',
            main: 'maneuver',
            salmon: false,
            aliases: ['5011'],
            xGroup: 'C-',
            releaseAt: '2023-12-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'hotblaster_custom',
            name: 'Custom Blaster',
            type: 'shooter',
            sub: 'pointsensor',
            special: 'ultra_chakuchi',
            main: 'hotblaster',
            salmon: false,
            aliases: ['211'],
            xGroup: 'D-',
            releaseAt: '2023-12-01T00:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('maneuver_collabo', salmon: false);
        $this->downWeapon3('hotblaster_custom', salmon: false);

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
