<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\GameMode;
use yii\db\Migration;

class m150923_120331_rule extends Migration
{
    public function up()
    {
        $this->createTable('game_mode', [
            'id' => $this->primaryKey(),
            'key' => $this->string(16)->notNull()->unique(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->createTable('rule', [
            'id' => $this->primarykey(),
            'mode_id' => $this->integer()->notNull(),
            'key' => $this->string(16)->notNull()->unique(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->addForeignKey('fk_rule_1', 'rule', 'mode_id', 'game_mode', 'id');

        $this->batchInsert('game_mode', ['key', 'name'], [
            [ 'regular', 'Regular Battle' ],
            [ 'gachi', 'Ranked Battle' ],
        ]);

        $modeRegular = GameMode::findOne(['key' => 'regular'])->id;
        $modeGachi = GameMode::findOne(['key' => 'gachi'])->id;
        $this->batchInsert('rule', ['key', 'mode_id', 'name'], [
            [ 'nawabari', $modeRegular, 'Turf War' ],
            [ 'area', $modeGachi, 'Splat Zones' ],
            [ 'yagura', $modeGachi, 'Tower Control' ],
            [ 'hoko', $modeGachi, 'Rainmaker'],
        ]);
    }

    public function down()
    {
        $this->dropTable('rule');
        $this->dropTable('game_mode');
    }
}
