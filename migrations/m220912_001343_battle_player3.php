<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220912_001343_battle_player3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%battle_player3}}', [
            'id' => $this->bigPrimaryKey()->notNull(),
            'battle_id' => $this->bigPkRef('{{%battle3}}')->notNull(),
            'is_our_team' => $this->boolean()->notNull(),
            'is_me' => $this->boolean()->notNull(),
            'rank_in_team' => $this->integer()->null(),
            'name' => $this->string(10)->null(),
            'number' => $this->integer()->null(),
            'weapon_id' => $this->pkRef('{{%weapon3}}')->null(),
            'inked' => $this->integer()->null(),
            'kill' => $this->integer()->null(),
            'assist' => $this->integer()->null(),
            'kill_or_assist' => $this->integer()->null(),
            'death' => $this->integer()->null(),
            'special' => $this->integer()->null(),
            'is_disconnected' => $this->boolean()->null(),
        ]);

        $this->createIndex('battle_player3_battle_id_key', '{{%battle_player3}}', 'battle_id', false);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%battle_player3}}');

        return true;
    }
}
