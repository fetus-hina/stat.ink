<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m260816_031744_pet_shooter extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'petshooter_replica',
            name: 'Plastic-Bottle Shot Replica',
            type: 'shooter',
            sub: 'splashbomb',
            special: 'tripletornado',
            main: 'sshooter',
            canonical: 'sshooter_collabo',
            salmon: false,
            aliases: [
                '48',
                self::name2key3('Plastic-Bottle Shot Replica'),
            ],
            releaseAt: '2026-08-20T10:00:00+09:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->downWeapon3('petshooter_replica', salmon: false);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%weapon3}}',
            '{{%weapon3_alias}}',
        ];
    }
}
