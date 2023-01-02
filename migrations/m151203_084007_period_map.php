<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151203_084007_period_map extends Migration
{
    public function up()
    {
        $this->createTable('period_map', [
            'id' => $this->primaryKey(),
            'period' => $this->integer()->notNull(),
            'rule_id' => $this->integer()->notNull(),
            'map_id' => $this->integer()->notNull(),
        ]);
        $this->createIndex('ix_period_map_1', 'period_map', 'period');
        $this->addForeignKey('fk_period_map_1', 'period_map', 'rule_id', 'rule', 'id');
        $this->addForeignKey('fk_period_map_2', 'period_map', 'map_id', 'map', 'id');
    }

    public function down()
    {
        $this->dropTable('period_map');
    }
}
