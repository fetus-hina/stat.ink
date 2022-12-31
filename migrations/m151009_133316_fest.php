<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151009_133316_fest extends Migration
{
    public function up()
    {
        $this->createTable('gender', [
            'id'    => 'INTEGER NOT NULL PRIMARY KEY', // See: ISO 5218
            'name'  => $this->string(16)->notNull()->unique(),
        ]);
        $this->batchInsert('gender', ['id', 'name'], [
            [ 1, 'Boy'  ], // ISO 5218, "1" is Male
            [ 2, 'Girl' ], // ISO 5218, "2" is Female
        ]);

        $this->createTable('fest_title', [
            'id'    => 'INTEGER NOT NULL PRIMARY KEY',
            'key'   => $this->string(16)->notNull()->unique(),
        ]);
        $this->batchInsert('fest_title', ['id', 'key'], [
            [ 1, 'fanboy' ],
            [ 2, 'friend' ],
            [ 3, 'defender' ],
            [ 4, 'champion' ],
            [ 5, 'king' ],
        ]);

        $this->createTable('fest_title_gender', [
            'title_id'  => $this->integer()->notNull(),
            'gender_id' => $this->integer()->notNull(),
            'name'      => $this->string(32)->notNull(),
        ]);
        $this->addPrimaryKey('pk_fest_title_gender', 'fest_title_gender', ['title_id', 'gender_id']);
        $this->addForeignKey('fk_fest_title_gender_1', 'fest_title_gender', 'title_id', 'fest_title', 'id');
        $this->addForeignKey('fk_fest_title_gender_2', 'fest_title_gender', 'gender_id', 'gender', 'id');
        $this->batchInsert('fest_title_gender', ['title_id', 'gender_id', 'name'], [
            [ 1, 1, '{0} Fanboy' ],
            [ 2, 1, '{0} Friend' ],
            [ 3, 1, '{0} Defender' ],
            [ 4, 1, '{0} Champion' ],
            [ 5, 1, '{0} King' ],
            [ 1, 2, '{1} Fangirl' ],
            [ 2, 2, '{1} Friend' ],
            [ 3, 2, '{1} Defender' ],
            [ 4, 2, '{1} Champion' ],
            [ 5, 2, '{1} Queen' ],
        ]);

        $this->execute(
            'ALTER TABLE {{battle}} ' . implode(', ', [
                'ADD COLUMN [[gender_id]] INTEGER',
                'ADD COLUMN [[fest_title_id]] INTEGER',
                'ADD COLUMN [[my_team_color_hue]] INTEGER',
                'ADD COLUMN [[his_team_color_hue]] INTEGER',
                'ADD COLUMN [[my_team_color_rgb]] CHAR(6)',
                'ADD COLUMN [[his_team_color_rgb]] CHAR(6)',
            ]),
        );
        $this->addForeignKey('fk_battle_9', 'battle', 'gender_id', 'gender', 'id');
        $this->addForeignKey('fk_battle_10', 'battle', 'fest_title_id', 'fest_title', 'id');
    }

    public function down()
    {
        $this->execute(
            'ALTER TABLE {{battle}} ' . implode(', ', [
                'DROP COLUMN [[gender_id]]',
                'DROP COLUMN [[fest_title_id]]',
                'DROP COLUMN [[my_team_color_hue]]',
                'DROP COLUMN [[his_team_color_hue]]',
                'DROP COLUMN [[my_team_color_rgb]]',
                'DROP COLUMN [[his_team_color_rgb]]',
            ]),
        );
        $this->dropTable('fest_title_gender');
        $this->dropTable('fest_title');
        $this->dropTable('gender');
    }
}
