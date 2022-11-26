<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m221126_192346_new_variants_202212 extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'promodeler_rg',
            name: 'Aerospray RG',
            type: 'shooter',
            sub: 'sprinkler',
            special: 'nicedama',
            main: 'promodeler_mg',
            aliases: ['31'],
        );
        $this->upWeapon3(
            key: 'sshooter_collabo',
            name: 'Tentatek Splattershot',
            type: 'shooter',
            sub: 'splashbomb',
            main: 'sshooter',
            aliases: ['41'],
        );
        $this->upWeapon3(
            key: 'bucketslosher_deco',
            name: 'Slosher Deco',
            type: 'slosher',
            sub: 'linemarker',
            special: 'shokuwander',
            main: 'bucketslosher',
            aliases: ['3001'],
        );
        $this->upWeapon3(
            key: 'splatspinner_collabo',
            name: 'Zink Mini Splatling',
            type: 'spinner',
            sub: 'poisonmist',
            special: 'greatbarrier',
            main: 'splatspinner',
            aliases: ['4001'],
        );
        $this->upWeapon3(
            key: 'sputtery_hue',
            name: 'Dapple Dualies Nouveau',
            type: 'maneuver',
            sub: 'torpedo',
            special: 'sameride',
            main: 'sputtery',
            aliases: ['5001'],
        );
        $this->upWeapon3(
            key: 'nova_neo',
            name: 'Luna Blaster Neo',
            type: 'blaster',
            sub: 'tansanbomb',
            special: 'ultrahanko',
            main: 'nova',
            aliases: ['201'],
        );
        $this->upWeapon3(
            key: 'pablo_hue',
            name: 'Inkbrush Nouveau',
            type: 'brush',
            sub: 'trap',
            special: 'ultrahanko',
            main: 'pablo',
            aliases: ['1101'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('promodeler_rg', salmon: false);
        $this->downWeapon3('sshooter_collabo', salmon: false);
        $this->downWeapon3('bucketslosher_deco', salmon: false);
        $this->downWeapon3('splatspinner_collabo', salmon: false);
        $this->downWeapon3('sputtery_hue', salmon: false);
        $this->downWeapon3('nova_neo', salmon: false);
        $this->downWeapon3('pablo_hue', salmon: false);

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
