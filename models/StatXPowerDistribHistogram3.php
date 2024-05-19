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
 * This is the model class for table "stat_x_power_distrib_histogram3".
 *
 * @property integer $season_id
 * @property integer $rule_id
 * @property integer $class_value
 * @property integer $users
 *
 * @property Rule3 $rule
 * @property Season3 $season
 */
class StatXPowerDistribHistogram3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_x_power_distrib_histogram3';
    }

    public function rules()
    {
        return [
            [['season_id', 'rule_id', 'class_value', 'users'], 'required'],
            [['season_id', 'rule_id', 'class_value', 'users'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'class_value', 'users'], 'integer'],
            [['season_id', 'rule_id', 'class_value'], 'unique', 'targetAttribute' => ['season_id', 'rule_id', 'class_value']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'class_value' => 'Class Value',
            'users' => 'Users',
        ];
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
