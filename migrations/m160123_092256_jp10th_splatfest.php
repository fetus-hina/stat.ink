<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use app\models\Splatfest;
use yii\db\Migration;

class m160123_092256_jp10th_splatfest extends Migration
{
    public function safeUp()
    {
        $festId = Splatfest::findOne([
            'region_id' => Region::findOne(['key' => 'jp'])->id,
            'order' => 10,
        ])->id;

        $this->update('splatfest_team', ['color_hue' => 110], ['fest_id' => $festId, 'team_id' => 1]);
        $this->update('splatfest_team', ['color_hue' => 22], ['fest_id' => $festId, 'team_id' => 2]);
    }

    public function safeDown()
    {
        $festId = Splatfest::findOne([
            'region_id' => Region::findOne(['key' => 'jp'])->id,
            'order' => 10,
        ])->id;

        $this->update('splatfest_team', ['color_hue' => null], ['fest_id' => $festId]);
    }
}
