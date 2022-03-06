<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use app\models\Splatfest;
use yii\db\Migration;

class m160423_105040_jp13th_splatfest extends Migration
{
    public function safeUp()
    {
        $this->insert('splatfest', [
            'region_id' => Region::findOne(['key' => 'jp'])->id,
            'name'      => 'ガンガンいこうぜ vs いのちだいじに',
            'start_at'  => '2016-04-23 09:00:00+09',
            'end_at'    => '2016-04-24 09:00:00+09',
            'order'     => 13,
        ]);
        $festId = Splatfest::find()
            ->joinWith('region', false)
            ->andWhere([
                '{{region}}.[[key]]' => 'jp',
                '{{splatfest}}.[[order]]' => 13,
            ])
            ->one()->id;

        $this->batchInsert('splatfest_team', ['fest_id', 'team_id', 'name', 'color_hue'], [
            [ $festId, 1, 'ツナマヨネーズ', 184 ],
            [ $festId, 2, '紅しゃけ', 352 ],
        ]);
    }

    public function safeDown()
    {
        $fest = Splatfest::find()
            ->joinWith('region', false)
            ->andWhere([
                '{{region}}.[[key]]' => 'jp',
                '{{splatfest}}.[[order]]' => 13,
            ])
            ->one();
        if (!$fest) {
            return false;
        }
        $this->delete('splatfest_team', ['fest_id' => $fest->id]);
        $this->delete('splatfest', ['id' => $fest->id]);
    }
}
