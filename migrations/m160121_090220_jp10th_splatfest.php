<?php
use yii\db\Migration;
use app\models\{
    Region,
    Splatfest,
    SplatfestTeam
};

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
