<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use app\models\Splatfest;
use yii\db\Migration;

class m160304_151540_jp12th_splatfest extends Migration
{
    public function safeUp()
    {
        $this->insert('splatfest', [
            'region_id' => Region::findOne(['key' => 'jp'])->id,
            'name' => 'ガンガンいこうぜ vs いのちだいじに',
            'start_at' => '2016-03-12 09:00:00+09',
            'end_at' => '2016-03-13 09:00:00+09',
            'order' => 12,
        ]);
        $festId = Splatfest::find()
            ->joinWith(['region'])
            ->andWhere([
                '{{region}}.[[key]]' => 'jp',
                '{{splatfest}}.[[order]]' => 12,
            ])
            ->one()->id;

        $this->batchInsert('splatfest_team', ['fest_id', 'team_id', 'name', 'color_hue'], [
            [ $festId, 1, 'ガンガンいこうぜ', 332],
            [ $festId, 2, 'いのちだいじに', 34],
        ]);
    }

    public function safeDown()
    {
        $fest = Splatfest::find()
            ->joinWith(['region'])
            ->andWhere([
                '{{region}}.[[key]]' => 'jp',
                '{{splatfest}}.[[order]]' => 12,
            ])
            ->one();
        if (!$fest) {
            return false;
        }
        $this->delete('splatfest_team', ['fest_id' => $fest->id]);
        $this->delete('splatfest', ['fest_id' => $fest->id]);
    }
}
