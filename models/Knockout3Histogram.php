<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "knockout3_histogram".
 *
 * @property integer $id
 * @property integer $season_id
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $class_value
 * @property integer $count
 *
 * @property Map3 $map
 * @property Rule3 $rule
 * @property Season3 $season
 */
class Knockout3Histogram extends ActiveRecord
{
    public static function tableName()
    {
        return 'knockout3_histogram';
    }

    public function rules()
    {
        return [
            [['season_id', 'rule_id', 'class_value', 'count'], 'required'],
            [['season_id', 'rule_id', 'map_id', 'class_value', 'count'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'map_id', 'class_value', 'count'], 'integer'],
            [['season_id', 'rule_id', 'COALESCE(map_id, 0)', 'class_value'], 'unique', 'targetAttribute' => ['season_id', 'rule_id', 'COALESCE(map_id, 0)', 'class_value']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => Map3::class, 'targetAttribute' => ['map_id' => 'id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'class_value' => 'Class Value',
            'count' => 'Count',
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
