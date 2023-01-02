<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151224_081303_splatfest_battle_summary extends Migration
{
    public function up()
    {
        $this->createTable('splatfest_battle_summary', [
            'fest_id' => 'INTEGER NOT NULL',
            'timestamp' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
            'alpha_win' => 'BIGINT NOT NULL',
            'alpha_lose' => 'BIGINT NOT NULL',
            'bravo_win' => 'BIGINT NOT NULL',
            'bravo_lose' => 'BIGINT NOT NULL',
            'summarized_at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);
        $this->addPrimaryKey('pk_splatfest_battle_summary', 'splatfest_battle_summary', ['fest_id', 'timestamp']);
        $this->addForeignKey('fk_splatfest_battle_summary_1', 'splatfest_battle_summary', 'fest_id', 'splatfest', 'id');
    }

    public function down()
    {
        $this->dropTable('splatfest_battle_summary');
    }
}
