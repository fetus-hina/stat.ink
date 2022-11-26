<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m221126_073742_s3_202212_new_weapon extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'spaceshooter',
            name: 'Splattershot Nova',
            type: 'shooter',
            sub: 'pointsensor',
            special: 'megaphone51',
        );
        $this->upWeapon3(
            key: 'wideroller',
            name: 'Big Swig Roller',
            type: 'roller',
        );
        $this->upWeapon3(
            key: 'rpen_5h',
            name: 'Snipewriter 5H',
            type: 'charger',
            special: 'energystand',
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('spaceshooter');
        $this->downWeapon3('wideroller');
        $this->downWeapon3('rpen_5h');
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
