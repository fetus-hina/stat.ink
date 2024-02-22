<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m240222_141306_order_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'order_blaster_replica',
            name: 'Order Blaster Replica',
            type: 'blaster',
            sub: 'splashbomb',
            special: 'shokuwander',
            main: 'nova',
            canonical: 'nova',
            salmon: false,
            aliases: ['205'],
            xGroup: 'D-',
            xGroup2: 'S',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_brush_replica',
            name: 'Orderbrush Replica',
            type: 'brush',
            sub: 'kyubanbomb',
            special: 'shokuwander',
            main: 'hokusai',
            canonical: 'hokusai',
            salmon: false,
            aliases: ['1115'],
            xGroup: 'E-',
            xGroup2: 'S',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_charger_replica',
            name: 'Order Charger Replica',
            type: 'charger',
            sub: 'splashbomb',
            special: 'kyuinki',
            main: 'splatcharger',
            canonical: 'splatcharger',
            salmon: false,
            aliases: ['2015'],
            xGroup: 'A+',
            xGroup2: 'C',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_maneuver_replica',
            name: 'Order Dualie Replicas',
            type: 'maneuver',
            sub: 'kyubanbomb',
            special: 'kanitank',
            main: 'maneuver',
            canonical: 'maneuver',
            salmon: false,
            aliases: ['5015'],
            xGroup: 'C-',
            xGroup2: 'S',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_roller_replica',
            name: 'Order Roller Replica',
            type: 'roller',
            sub: 'curlingbomb',
            special: 'greatbarrier',
            main: 'splatroller',
            canonical: 'splatroller',
            salmon: false,
            aliases: ['1015'],
            xGroup: 'E-',
            xGroup2: 'S',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_wiper_replica',
            name: 'Order Splatana Replica',
            type: 'wiper',
            sub: 'quickbomb',
            special: 'shokuwander',
            main: 'jimuwiper',
            canonical: 'jimuwiper',
            salmon: false,
            aliases: ['8005'],
            xGroup: 'E+',
            xGroup2: 'M',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_shelter_replica',
            name: 'Order Brella Replica',
            type: 'brella',
            sub: 'sprinkler',
            special: 'tripletornado',
            main: 'parashelter',
            canonical: 'parashelter',
            salmon: false,
            aliases: ['6005'],
            xGroup: 'E-',
            xGroup2: 'S',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_shooter_replica',
            name: 'Order Shot Replica',
            type: 'shooter',
            sub: 'kyubanbomb',
            special: 'ultrashot',
            main: 'sshooter',
            canonical: 'sshooter',
            salmon: false,
            aliases: ['47'],
            xGroup: 'C-',
            xGroup2: 'S',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_slosher_replica',
            name: 'Order Slosher Replica',
            type: 'slosher',
            sub: 'splashbomb',
            special: 'tripletornado',
            main: 'bucketslosher',
            canonical: 'bucketslosher',
            salmon: false,
            aliases: ['3005'],
            xGroup: 'D-',
            xGroup2: 'M',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_spinner_replica',
            name: 'Order Splatling Replica',
            type: 'spinner',
            sub: 'sprinkler',
            special: 'hopsonar',
            main: 'barrelspinner',
            canonical: 'barrelspinner',
            salmon: false,
            aliases: ['4015'],
            xGroup: 'B',
            xGroup2: 'L',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );
        $this->upWeapon3(
            key: 'order_stringer_replica',
            name: 'Order Stringer Replica',
            type: 'stringer',
            sub: 'poisonmist',
            special: 'megaphone51',
            main: 'tristringer',
            canonical: 'tristringer',
            salmon: false,
            aliases: ['7015'],
            xGroup: 'B',
            xGroup2: 'L',
            releaseAt: '2024-02-22T02:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $keys = [
            'order_blaster_replica',
            'order_brush_replica',
            'order_charger_replica',
            'order_maneuver_replica',
            'order_roller_replica',
            'order_shelter_replica',
            'order_shooter_replica',
            'order_slosher_replica',
            'order_spinner_replica',
            'order_stringer_replica',
            'order_wiper_replica',
        ];

        foreach ($keys as $key) {
            $this->downWeapon3($key, salmon: false);
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
