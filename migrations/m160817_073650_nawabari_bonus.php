<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160817_073650_nawabari_bonus extends Migration
{
    public function up()
    {
        $this->createTable('turfwar_win_bonus', [
            'id' => $this->primaryKey(),
            'bonus' => $this->integer()->notNull(),
            'start_at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);
        $this->batchInsert('turfwar_win_bonus', ['bonus', 'start_at'], [
            [ 300, '2015-05-28 00:00:00+09' ],
            [ 1000, '2016-07-24 19:03:00+09' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('turfwar_win_bonus');
    }
}
