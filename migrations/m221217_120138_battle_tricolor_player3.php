<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221217_120138_battle_tricolor_player3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%battle_tricolor_player3}}', [
            'id' => $this->bigPrimaryKey(),
            'battle_id' => $this->bigPkRef('{{%battle3}}')->notNull(),
            'team' => $this->integer()->notNull(),
            'rank_in_team' => $this->integer()->null(),
            'name' => $this->string(10)->null(),
            'weapon_id' => $this->pkRef('{{%weapon3}}')->null(),
            'inked' => $this->integer()->null(),
            'kill' => $this->integer()->null(),
            'assist' => $this->integer()->null(),
            'special' => $this->integer()->null(),
            'signal' => $this->integer()->null(),
            'is_disconnected' => $this->boolean()->null(),
            'splashtag_title_id' => $this->pkRef('{{%splashtag_title3}}')->null(),
            'number' => $this->string(32)->null(),
            'headgear_id' => $this->pkRef('{{%gear_configuration3}}')->null(),
            'clothing_id' => $this->pkRef('{{%gear_configuration3}}')->null(),
            'shoes_id' => $this->pkRef('{{%gear_configuration3}}')->null(),
            'is_crowned' => $this->boolean()->null(),
        ]);

        $this->createIndex(
            'battle_tricolor_player3_battle_id_team_rank',
            '{{%battle_tricolor_player3}}',
            ['battle_id', 'team', 'rank_in_team'],
            false,
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%battle_tricolor_player3}}');

        return true;
    }
}
