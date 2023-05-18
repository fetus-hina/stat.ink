<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230517_121947_sblast92 extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'sblast92',
            name: 'S-BLAST \'92',
            type: 'blaster',
            sub: 'sprinkler',
            special: 'sameride',
            salmon: true,
            aliases: [
                '260', // guess
            ],
            xGroup: 'D-', // guess
            releaseAt: '2023-06-01T00:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('sblast92', salmon: true);

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
