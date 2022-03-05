<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use app\models\Splatfest;
use yii\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m160722_075551_16th_splatfest extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatfest', ['region_id', 'name', 'start_at', 'end_at', 'order'], [
            [
                'region_id' => Region::findOne(['key' => 'jp'])->id,
                'name'      => 'アオリ vs ホタル',
                'start_at'  => '2016-07-22 12:00:00+09',
                'end_at'    => '2016-07-24 12:00:00+09',
                'order'     => 16,
            ],
            [
                'region_id' => Region::findOne(['key' => 'eu'])->id,
                'name'      => 'Callie vs Marie',
                'start_at'  => '2016-07-22 12:00:00+09',
                'end_at'    => '2016-07-24 12:00:00+09',
                'order'     => 16,
            ],
            [
                'region_id' => Region::findOne(['key' => 'na'])->id,
                'name'      => 'Callie vs Marie',
                'start_at'  => '2016-07-22 12:00:00+09',
                'end_at'    => '2016-07-24 12:00:00+09',
                'order'     => 16,
            ],
        ]);
        $ids = ArrayHelper::map(
            (new Query())
                ->select([
                    'id' => '{{splatfest}}.[[id]]',
                    'region' => '{{region}}.[[key]]',
                ])
                ->from('splatfest')
                ->innerJoin('region', '{{splatfest}}.[[region_id]] = {{region}}.[[id]]')
                ->where(['{{splatfest}}.[[order]]' => 16])
                ->all(),
            'region',
            'id'
        );
        $this->batchInsert(
            'splatfest_team',
            ['fest_id', 'team_id', 'name', 'color_hue'],
            [
                [ $ids['jp'], 1, 'アオリ', 305],
                [ $ids['jp'], 2, 'ホタル',  97],
                [ $ids['eu'], 1, 'Callie', 305],
                [ $ids['eu'], 2, 'Marie',   97],
                [ $ids['na'], 1, 'Callie', 305],
                [ $ids['na'], 2, 'Marie',   97],
            ]
        );
    }

    public function safeDown()
    {
        $ids = ArrayHelper::getColumn(
            Splatfest::find()->asArray()->where(['order' => 16])->all(),
            'id'
        );
        $this->delete('splatfest_team', ['fest_id' => $ids]);
        $this->delete('splatfest', ['id' => $ids]);
    }
}
