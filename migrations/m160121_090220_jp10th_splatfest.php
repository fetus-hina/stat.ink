<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use app\models\Splatfest;
use app\models\SplatfestTeam;
use yii\db\Migration;

class m160121_090220_jp10th_splatfest extends Migration
{
    public function safeUp()
    {
        $model = new Splatfest();
        $model->attributes = [
            'region_id' => Region::findOne(['key' => 'jp'])->id,
            'name'      => 'カンペキなカラダ vs カンペキな頭脳',
            'start_at'  => '2016-01-23 12:00:00+09',
            'end_at'    => '2016-01-24 12:00:00+09',
            'order'     => 10,
        ];
        if (!$model->save()) {
            return false;
        }

        $team = new SplatfestTeam();
        $team->attributes = [
            'fest_id' => $model->id,
            'team_id' => 1,
            'name'    => 'カンペキなカラダ',
        ];
        if (!$team->save()) {
            return false;
        }

        $team = new SplatfestTeam();
        $team->attributes = [
            'fest_id' => $model->id,
            'team_id' => 2,
            'name'    => 'カンペキな頭脳',
        ];
        if (!$team->save()) {
            return false;
        }
    }

    public function safeDown()
    {
        $fest = Splatfest::findOne([
            'region_id' => Region::findOne(['key' => 'jp'])->id,
            'order' => 10,
        ]);
        if (!$fest) {
            return false;
        }
        SplatfestTeam::deleteAll(['fest_id' => $fest->id]);
        if (!$fest->delete()) {
            return false;
        }
    }
}
