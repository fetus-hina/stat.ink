<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "knockout3".
 *
 * @property integer $id
 * @property integer $season_id
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $battles
 * @property integer $knockout
 * @property double $avg_battle_time
 * @property double $stddev_battle_time
 * @property double $avg_knockout_time
 * @property double $stddev_knockout_time
 * @property integer $histogram_width
 *
 * @property Map3 $map
 * @property Rule3 $rule
 * @property Season3 $season
 */
class Knockout3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'knockout3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['map_id', 'avg_battle_time', 'stddev_battle_time', 'avg_knockout_time', 'stddev_knockout_time', 'histogram_width'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'battles', 'knockout'], 'required'],
            [['season_id', 'rule_id', 'map_id', 'battles', 'knockout', 'histogram_width'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'map_id', 'battles', 'knockout', 'histogram_width'], 'integer'],
            [['avg_battle_time', 'stddev_battle_time', 'avg_knockout_time', 'stddev_knockout_time'], 'number'],
            [['season_id', 'rule_id', 'COALESCE(map_id, 0)'], 'unique', 'targetAttribute' => ['season_id', 'rule_id', 'COALESCE(map_id, 0)']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => Map3::class, 'targetAttribute' => ['map_id' => 'id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'battles' => 'Battles',
            'knockout' => 'Knockout',
            'avg_battle_time' => 'Avg Battle Time',
            'stddev_battle_time' => 'Stddev Battle Time',
            'avg_knockout_time' => 'Avg Knockout Time',
            'stddev_knockout_time' => 'Stddev Knockout Time',
            'histogram_width' => 'Histogram Width',
        ];
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(Map3::class, ['id' => 'map_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getSeason(): ActiveQuery
    {
        return $this->hasOne(Season3::class, ['id' => 'season_id']);
    }
}
