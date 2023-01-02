<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160115_130125_knockout extends Migration
{
    public function up()
    {
        $this->createTable('knockout', [
            'map_id' => $this->integer()->notNull(),
            'rule_id' => $this->integer()->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'knockouts' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_knockout', 'knockout', ['map_id', 'rule_id']);
        $this->addForeignKey('fk_knockout_1', 'knockout', 'map_id', 'map', 'id');
        $this->addForeignKey('fk_knockout_2', 'knockout', 'rule_id', 'rule', 'id');
    }

    public function down()
    {
        $this->dropTable('knockout');
    }
}
