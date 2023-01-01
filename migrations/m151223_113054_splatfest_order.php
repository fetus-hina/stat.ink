<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use yii\db\Migration;

class m151223_113054_splatfest_order extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{splatfest}} ADD COLUMN [[order]] INTEGER');
        $transaction = Yii::$app->db->beginTransaction();
        $jp = Region::findOne(['key' => 'jp'])->id;
        $jpStarts = [
            '2015-06-13 18:00:00+09',
            '2015-07-03 15:00:00+09',
            '2015-07-25 15:00:00+09',
            '2015-08-22 12:00:00+09',
            '2015-09-12 12:00:00+09',
            '2015-10-10 09:00:00+09',
            '2015-10-31 09:00:00+09',
            '2015-11-21 12:00:00+09',
            '2015-12-26 09:00:00+09',
        ];
        foreach ($jpStarts as $i => $startAt) {
            $this->update(
                'splatfest',
                ['order' => $i + 1],
                ['region_id' => $jp, 'start_at' => $startAt],
            );
        }
        $transaction->commit();
        $this->execute('ALTER TABLE {{splatfest}} ALTER COLUMN [[order]] SET NOT NULL');
        $this->createIndex('ix_splatfest_1', 'splatfest', ['region_id', 'order'], true);
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{splatfest}} DROP COLUMN [[order]]');
    }
}
