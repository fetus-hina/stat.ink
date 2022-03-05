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

class m160220_030838_jp11th_splatfest extends Migration
{
    public function safeUp()
    {
        $model = new Splatfest();
        $model->attributes = [
            'region_id' => Region::findOne(['key' => 'jp'])->id,
            'name'      => 'ポケットモンスター 赤vs緑',
            'start_at'  => '2016-02-20 06:00:00+09',
            'end_at'    => '2016-02-21 06:00:00+09',
            'order'     => 11,
        ];
        if (!$model->save()) {
            return false;
        }

        $team = new SplatfestTeam();
        $team->attributes = [
            'fest_id' => $model->id,
            'team_id' => 1,
            'name'    => 'ポケットモンスター赤',
            'color_hue' => 348,
        ];
        if (!$team->save()) {
            return false;
        }

        $team = new SplatfestTeam();
        $team->attributes = [
            'fest_id' => $model->id,
            'team_id' => 2,
            'name'    => 'ポケットモンスター緑',
            'color_hue' => 156,
        ];
        if (!$team->save()) {
            return false;
        }
    }

    public function safeDown()
    {
        $fest = Splatfest::findOne([
            'region_id' => Region::findOne(['key' => 'jp'])->id,
            'order' => 11,
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
