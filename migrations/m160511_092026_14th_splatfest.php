<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use app\models\Splatfest;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

class m160511_092026_14th_splatfest extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatfest', ['region_id', 'name', 'start_at', 'end_at', 'order'], [
            [
                'region_id' => Region::findOne(['key' => 'jp'])->id,
                'name' => 'オシャレなパーティー vs コスプレパーティー',
                'start_at' => '2016-05-14 12:00:00+09',
                'end_at' => '2016-05-15 19:00:00+09',
                'order' => 14,
            ],
            [
                'region_id' => Region::findOne(['key' => 'eu'])->id,
                'name' => 'Black Tie Event vs Fancy Dress Party',
                'start_at' => '2016-05-14 12:00:00+09',
                'end_at' => '2016-05-15 19:00:00+09',
                'order' => 14,
            ],
            [
                'region_id' => Region::findOne(['key' => 'na'])->id,
                'name' => 'Fancy Party vs Costume Party',
                'start_at' => '2016-05-14 12:00:00+09',
                'end_at' => '2016-05-15 19:00:00+09',
                'order' => 14,
            ],
        ]);
        $ids = ArrayHelper::map(
            (new \yii\db\Query())
                ->select([
                    'id' => '{{splatfest}}.[[id]]',
                    'region' => '{{region}}.[[key]]',
                ])
                ->from('splatfest')
                ->innerJoin('region', '{{splatfest}}.[[region_id]] = {{region}}.[[id]]')
                ->where(['{{splatfest}}.[[order]]' => 14])
                ->all(),
            'region',
            'id',
        );
        $this->batchInsert(
            'splatfest_team',
            ['fest_id', 'team_id', 'name'],
            [
                [ $ids['jp'], 1, 'オシャレなパーティー' ],
                [ $ids['jp'], 2, 'コスプレパーティー' ],
                [ $ids['eu'], 1, 'Black Tie Event' ],
                [ $ids['eu'], 2, 'Fancy Dress Party' ],
                [ $ids['na'], 1, 'Fancy Party ' ],
                [ $ids['na'], 2, 'Costume Party' ],
            ],
        );
    }

    public function safeDown()
    {
        $ids = ArrayHelper::getColumn(
            Splatfest::find()->asArray()->where(['order' => 14])->all(),
            'id',
        );
        $this->delete('splatfest_team', ['fest_id' => $ids]);
        $this->delete('splatfest', ['id' => $ids]);
    }
}
