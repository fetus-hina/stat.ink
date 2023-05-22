<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;

final class m230522_225757_h3d_rapielideco extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upWeapon3(
            key: 'h3reelgun_d',
            name: 'H-3 Nozzlenose D',
            type: 'reelgun',
            sub: 'splashshield',
            special: 'greatbarrier',
            main: 'h3reelgun',
            salmon: false,
            aliases: ['311'],
            xGroup: 'C+',
            releaseAt: '2023-06-01T00:00:00+00:00',
        );

        $this->upWeapon3(
            key: 'rapid_elite_deco',
            name: 'Rapid Blaster Pro Deco',
            type: 'blaster',
            sub: 'linemarker',
            special: 'megaphone51',
            main: 'rapid_elite',
            salmon: false,
            aliases: ['251'],
            xGroup: 'D+',
            releaseAt: '2023-06-01T00:00:00+00:00',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downWeapon3('h3reelgun_d', salmon: false);
        $this->downWeapon3('rapid_elite_deco', salmon: false);

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
