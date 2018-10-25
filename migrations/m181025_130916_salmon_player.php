<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181025_130916_salmon_player extends Migration
{
    public function up()
    {
        $this->createTable('salmon_player2', [
            'id' => $this->primaryKey(),
            'work_id' => $this->pkRef('salmon2')->notNull(),
            'is_me' => $this->boolean()->notNull(),
            'splatnet_id' => $this->string(16)->null(),
            'name' => $this->string(10)->null(),
            'special_id' => $this->pkRef('salmon_special2')->null(),
            'rescue' => $this->integer()->null()->check('[[rescue]] >= 0'),
            'death' => $this->integer()->null()->check('[[death]] >= 0'),
            'golden_egg_delivered' => $this->integer()->null()->check('[[golden_egg_delivered]] >= 0'),
            'power_egg_collected' => $this->integer()->null()->check('[[power_egg_collected]] >= 0'),
            'species_id' => $this->pkRef('species2')->null(),
            'gender_id' => $this->pkRef('gender')->null(),
        ]);
        $this->createTable('salmon_player_special_use2', [
            'player_id' => $this->pkRef('salmon_player2')->notNull(),
            'wave' => $this->integer()->notNull()->check('[[wave]] BETWEEN 1 AND 3'),
            'count' => $this->integer()->notNull()->check('[[count]] BETWEEN 0 AND 3'),
            'PRIMARY KEY ([[player_id]], [[wave]])',
        ]);
        $this->createTable('salmon_player_weapon2', [
            'player_id' => $this->pkRef('salmon_player2')->notNull(),
            'wave' => $this->integer()->notNull()->check('[[wave]] BETWEEN 1 AND 3'),
            'weapon_id' => $this->pkref('salmon_main_weapon2')->notNull(),
            'PRIMARY KEY ([[player_id]], [[wave]])',
        ]);
        $this->createTable('salmon_player_boss_kill2', [
            'player_id' => $this->pkRef('salmon_player2')->notNull(),
            'boss_id' => $this->pkRef('salmon_boss2')->notNull(),
            'count' => $this->integer()->notNull()->check('[[count]] > 0'),
            'PRIMARY KEY ([[player_id]], [[boss_id]])',
        ]);
    }

    public function down()
    {
        $this->dropTables(array_reverse([
            'salmon_player2',
            'salmon_player_special_use2',
            'salmon_player_weapon2',
            'salmon_player_boss_kill2',
        ]));
    }
}
