<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_x_power_distrib3".
 *
 * @property integer $season_id
 * @property integer $rule_id
 * @property integer $x_power
 * @property integer $users
 *
 * @property Rule3 $rule
 * @property Season3 $season
 */
class StatXPowerDistrib3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_x_power_distrib3';
    }

    public function rules()
    {
        return [
            [['season_id', 'rule_id', 'x_power', 'users'], 'required'],
            [['season_id', 'rule_id', 'x_power', 'users'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'x_power', 'users'], 'integer'],
            [['season_id', 'rule_id', 'x_power'], 'unique', 'targetAttribute' => ['season_id', 'rule_id', 'x_power']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'x_power' => 'X Power',
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
