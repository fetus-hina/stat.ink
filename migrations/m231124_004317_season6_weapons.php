<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m231124_004317_season6_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'fincent_hue',
            name: 'Painbrush Nouveau',
            type: 'brush',
            sub: 'pointsensor',
            special: 'missile',
            main: 'fincent',
            salmon: false,
            aliases: [
                '1121',
                'vincent_hue',
            ],
            xGroup: 'E+',
            releaseAt: '2023-12-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'lact450_deco',
            name: 'REEF-LUX 450 Deco',
            type: 'stringer',
            sub: 'splashshield',
            special: 'sameride',
            main: 'lact450',
            salmon: false,
            aliases: ['7021'],
            xGroup: 'C+',
            releaseAt: '2023-12-01T00:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('fincent_hue', salmon: false);
        $this->downWeapon3('lact450_deco', salmon: false);

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
