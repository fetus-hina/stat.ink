<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Splatfest;
use yii\db\Migration;

class m160312_003018_jp12th_splatfest_color extends Migration
{
    public function safeUp()
    {
        $fest = Splatfest::find()
            ->joinWith('region')
            ->where([
                'region.key' => 'jp',
                'splatfest.order' => 12,
            ])
            ->limit(1)
            ->one();

        $this->update(
            'splatfest_team',
            ['color_hue' => 338],
            ['fest_id' => $fest->id, 'team_id' => 1],
        );
        $this->update(
            'splatfest_team',
            ['color_hue' => 50],
            ['fest_id' => $fest->id, 'team_id' => 2],
        );
        $this->delete(
            'splatfest_battle_summary',
            ['fest_id' => $fest->id],
        );
    }

    public function safeDown()
    {
        $fest = Splatfest::find()
            ->joinWith('region')
            ->where([
                'region.key' => 'jp',
                'splatfest.order' => 12,
            ])
            ->limit(1)
            ->one();

        $this->update(
            'splatfest_team',
            ['color_hue' => 332],
            ['fest_id' => $fest->id, 'team_id' => 1],
        );
        $this->update(
            'splatfest_team',
            ['color_hue' => 34],
            ['fest_id' => $fest->id, 'team_id' => 2],
        );
        $this->delete(
            'splatfest_battle_summary',
            ['fest_id' => $fest->id],
        );
    }
}
