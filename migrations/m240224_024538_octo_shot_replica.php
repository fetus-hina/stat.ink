<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m240224_024538_octo_shot_replica extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->delete('{{%weapon3_alias}}', ['key' => '46']);

        $this->upWeapon3(
            key: 'octoshooter_replica',
            name: 'Octo Shot Replica',
            type: 'shooter',
            sub: 'splashbomb',
            special: 'tripletornado',
            main: 'sshooter',
            canonical: 'sshooter_collabo',
            salmon: false,
            aliases: ['46'],
            xGroup: 'C-',
            xGroup2: 'S',
            releaseAt: '2022-02-22T02:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('octoshooter_replica', salmon: false);
        $this->insert('{{%weapon3_alias}}', [
            'weapon_id' => $this->key2id('{{%weapon3}}', 'order_shooter_replica'),
            'key' => '46',
        ]);

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
