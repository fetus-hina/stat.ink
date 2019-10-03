<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170813_100235_map2_splatnet extends Migration
{
    public function up()
    {
        $this->addColumn('map2', 'splatnet', 'INTEGER NULL');

        $map = [
            0 => 'battera',
            1 => 'fujitsubo',
            2 => 'gangaze',
            3 => 'chozame',
            4 => 'ama',
            5 => 'kombu',
            7 => 'hokke',
            8 => 'tachiuo',
            9999 => 'mystery',
        ];
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($map as $id => $key) {
            $this->update('map2', ['splatnet' => $id], ['key' => $key]);
        }
        $transaction->commit();
    }

    public function down()
    {
        $this->dropColumn('map2', 'splatnet');
    }
}
