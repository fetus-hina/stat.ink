<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use app\models\Splatfest;
use yii\db\Migration;

class m160616_120644_jp15th_splatfest extends Migration
{
    public function safeUp()
    {
        $this->insert('splatfest', [
            'region_id' => Region::findOne(['key' => 'jp'])->id,
            'name' => 'きのこの山 vs たけのこの里',
            'start_at' => '2016-06-18 09:00:00+09',
            'end_at' => '2016-06-19 09:00:00+09',
            'order' => 15,
        ]);
        $festId = Splatfest::find()
            ->joinWith('region', false)
            ->andWhere([
                '{{region}}.[[key]]' => 'jp',
                '{{splatfest}}.[[order]]' => 15,
            ])
            ->one()->id;

        $this->batchInsert('splatfest_team', ['fest_id', 'team_id', 'name'], [
            [ $festId, 1, 'きのこの山' ],
            [ $festId, 2, 'たけのこの里' ],
        ]);
    }

    public function safeDown()
    {
        $fest = Splatfest::find()
            ->joinWith('region', false)
            ->andWhere([
                '{{region}}.[[key]]' => 'jp',
                '{{splatfest}}.[[order]]' => 15,
            ])
            ->one();
        if (!$fest) {
            return false;
        }
        $this->delete('splatfest_team', ['fest_id' => $fest->id]);
        $this->delete('splatfest', ['id' => $fest->id]);
    }
}
