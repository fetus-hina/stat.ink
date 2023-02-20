<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230220_185504_zf_splat_charger extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'splatcharger_collabo',
            name: 'Z+F Splat Charger',
            type: 'charger',
            sub: 'splashshield',
            special: 'tripletornado',
            main: 'splatcharger',
            salmon: false,
            aliases: [
                '2011',
            ],
            xGroup: 'A+',
        );

        $this->upWeapon3(
            key: 'splatscope_collabo',
            name: 'Z+F Splatterscope',
            type: 'charger',
            sub: 'splashshield',
            special: 'tripletornado',
            main: 'splatscope',
            salmon: false,
            aliases: [
                '2021',
            ],
            xGroup: 'A+',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('splatcharger_collabo', salmon: false);
        $this->downWeapon3('splatscope_collabo', salmon: false);

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
