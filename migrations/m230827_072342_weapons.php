<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230827_072342_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'moprin',
            name: 'Dread Wringer',
            type: 'slosher',
            sub: 'kyubanbomb',
            special: 'sameride',
            salmon: true,
            aliases: ['3050'],
            xGroup: 'D+',
            releaseAt: '2023-09-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'dynamo_tesla',
            name: 'Gold Dynamo Roller',
            type: 'shooter',
            sub: 'splashbomb',
            special: 'decoy',
            main: 'dynamo',
            salmon: false,
            aliases: ['1021'],
            xGroup: 'E+',
            releaseAt: '2023-09-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'screwslosher_neo',
            name: 'Sloshing Machine Neo',
            type: 'slosher',
            sub: 'pointsensor',
            special: 'ultrashot',
            main: 'screwslosher',
            salmon: false,
            aliases: ['3021'],
            xGroup: 'D-',
            releaseAt: '2023-09-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'tristringer_collabo',
            name: 'Inkline Tri-Stringer',
            type: 'stringer',
            sub: 'sprinkler',
            special: 'decoy',
            main: 'tristringer',
            salmon: false,
            aliases: ['7011'],
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
        $this->downWeapon3('moprin', salmon: true);
        $this->downWeapon3('dynamo_tesla', salmon: false);
        $this->downWeapon3('screwslosher_neo', salmon: false);
        $this->downWeapon3('tristringer_collabo', salmon: false);

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
