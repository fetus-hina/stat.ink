<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_x_power_distrib_abstract3".
 *
 * @property integer $season_id
 * @property integer $rule_id
 * @property integer $users
 * @property double $average
 * @property double $stddev
 * @property string $median
 *
 * @property Rule3 $rule
 * @property Season3 $season
 */
class StatXPowerDistribAbstract3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_x_power_distrib_abstract3';
    }

    public function rules()
    {
        return [
            [['season_id', 'rule_id', 'users', 'average'], 'required'],
            [['season_id', 'rule_id', 'users'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'users'], 'integer'],
            [['average', 'stddev', 'median'], 'number'],
            [['season_id', 'rule_id'], 'unique', 'targetAttribute' => ['season_id', 'rule_id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'users' => 'Users',
            'average' => 'Average',
            'stddev' => 'Stddev',
            'median' => 'Median',
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
