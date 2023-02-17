<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230217_141122_decoy_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'nzap89',
            name: 'N-ZAP \'89',
            type: 'shooter',
            sub: 'robotbomb',
            special: 'decoy',
            main: 'nzap85',
            salmon: false,
            aliases: [
                '61',
                'n_zap_89',
            ],
            xGroup: 'C-',
        );
        $this->upWeapon3(
            key: 'clashblaster_neo',
            name: 'Clash Blaster Neo',
            type: 'blaster',
            sub: 'curlingbomb',
            special: 'decoy',
            main: 'clashblaster',
            salmon: false,
            aliases: [
                '231',
                'clash_blaster_neo',
            ],
            xGroup: 'D-',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('nzap89', salmon: false);
        $this->downWeapon3('clashblaster_neo', salmon: false);

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
