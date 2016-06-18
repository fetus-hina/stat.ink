<?php
use yii\db\Migration;
use yii\helpers\ArrayHelper;
use app\models\Splatfest;

class m160618_002838_jp15th_splatfest_color extends Migration
{
    public function safeUp()
    {
        $this->update(
            'splatfest_team',
            ['color_hue' => 41],
            ['fest_id' => $this->festIdList, 'team_id' => 1]
        );
        $this->update(
            'splatfest_team',
            ['color_hue' => 107],
            ['fest_id' => $this->festIdList, 'team_id' => 2]
        );
    }

    public function safeDown()
    {
        $this->update(
            'splatfest_team',
            ['color_hue' => null],
            ['fest_id' => $this->festIdList]
        );
    }

    public function getFestIdList()
    {
        return ArrayHelper::getColumn(
            Splatfest::find()
                ->innerJoinWith('region', false)
                ->where([
                    '{{splatfest}}.[[order]]' => 15,
                    '{{region}}.[[key]]' => 'jp',
                ])
                ->asArray()
                ->all(),
            'id'
        );
    }
}
