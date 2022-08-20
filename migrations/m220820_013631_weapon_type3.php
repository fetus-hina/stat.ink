<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220820_013631_weapon_type3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%weapon_type3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3(),
            'name' => $this->string(48)->notNull()->unique(),
            'rank' => $this->integer()->notNull()->unique(),
        ]);

        $this->batchInsert(
            '{{%weapon_type3}}',
            ['key', 'name', 'rank'],
            [
                ['shooter', 'Shooters', 110],
                ['blaster', 'Blasters', 120],
                ['reelgun', 'Nozzlenoses', 130],
                ['maneuver', 'Dualies', 140],
                ['roller', 'Rollers', 210],
                ['brush', 'Brushes', 220],
                ['wiper', 'Splatanas', 230],
                ['charger', 'Chargers', 310],
                ['slosher', 'Sloshers', 410],
                ['spinner', 'Splatlings', 510],
                ['brella', 'Brellas', 610],
                ['stringer', 'Stringers', 710],
            ],
        );

        return true;
    }

    public function vacuumTables(): array
    {
        return [
            '{{%weapon_type3}}',
        ];
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%weapon_type3}}');

        return true;
    }
}
