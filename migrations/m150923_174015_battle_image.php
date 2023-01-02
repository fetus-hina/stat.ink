<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m150923_174015_battle_image extends Migration
{
    public function up()
    {
        $this->createTable('battle_image_type', [
            'id' => $this->integer()->notNull(),
            'name' => $this->string(16)->notNull()->unique(),
        ]);
        $this->addPrimaryKey('pk_battle_image_type', 'battle_image_type', 'id');
        $this->batchInsert('battle_image_type', ['id', 'name'], [
            [1, 'ジャッジ'],
            [2, '成績一覧'],
        ]);

        $this->createTable('battle_image', [
            'id' => $this->bigPrimaryKey(),
            'battle_id' => $this->bigInteger()->notNull(),
            'type_id' => $this->integer()->notNull(),
            'filename' => $this->string(64)->notNull(),
        ]);
        $this->addForeignKey('fk_battle_image_1', 'battle_image', 'battle_id', 'battle', 'id', 'RESTRICT');
        $this->addForeignKey('fk_battle_image_2', 'battle_image', 'type_id', 'battle_image_type', 'id', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('battle_image');
        $this->dropTable('battle_image_type');
    }
}
