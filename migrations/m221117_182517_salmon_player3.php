<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221117_182517_salmon_player3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_player3}}', [
            'id' => $this->bigPrimaryKey(),
            'salmon_id' => $this->bigPkRef('{{%salmon3}}')->notNull(),
            'is_me' => $this->boolean()->notNull(),
            'name' => $this->string(10)->null(),
            'number' => $this->string(32)->null(),
            'splashtag_title_id' => $this->pkRef('{{%splashtag_title3}}')->null(),
            'uniform_id' => $this->pkRef('{{%salmon_uniform3}}')->null(),
            'special_id' => $this->pkRef('{{%special3}}')->null(),
            'golden_eggs' => $this->integer()->null(),
            'golden_assist' => $this->integer()->null(),
            'power_eggs' => $this->integer()->null(),
            'rescue' => $this->integer()->null(),
            'rescued' => $this->integer()->null(),
            'defeat_boss' => $this->integer()->null(),
            'is_disconnected' => $this->boolean()->notNull(),

            'UNIQUE ([[salmon_id]], [[id]])',
        ]);

        $this->createTable('{{%salmon_player_weapon3}}', [
            'player_id' => $this->bigPkRef('{{%salmon_player3}}')->notNull(),
            'wave' => $this->integer()->notNull(),
            'weapon_id' => $this->pkRef('{{%salmon_weapon3}}')->null(),

            'PRIMARY KEY ([[player_id]], [[wave]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%salmon_player_weapon3}}',
            '{{%salmon_player3}}',
        ]);

        return true;
    }
}
