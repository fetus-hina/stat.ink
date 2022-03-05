<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Splatfest;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

class m160514_053244_14th_splatfest_color extends Migration
{
    public function safeUp()
    {
        $this->update(
            'splatfest_team',
            ['color_hue' => 169],
            ['fest_id' => $this->festIdList, 'team_id' => 1]
        );
        $this->update(
            'splatfest_team',
            ['color_hue' => 33],
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
                ->where(['{{splatfest}}.[[order]]' => 14])
                ->asArray()
                ->all(),
            'id'
        );
    }
}
