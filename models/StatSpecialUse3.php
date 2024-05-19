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
 * This is the model class for table "stat_special_use3".
 *
 * @property integer $id
 * @property integer $season_id
 * @property integer $rule_id
 * @property integer $special_id
 * @property integer $sample_size
 * @property integer $win
 * @property double $avg_uses
 * @property double $stddev
 * @property integer $percentile_5
 * @property integer $percentile_25
 * @property integer $percentile_50
 * @property integer $percentile_75
 * @property integer $percentile_95
 * @property integer $percentile_100
 *
 * @property Rule3 $rule
 * @property Season3 $season
 * @property Special3 $special
 */
class StatSpecialUse3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_special_use3';
    }

    public function rules()
    {
        return [
            [['season_id', 'special_id', 'sample_size', 'win', 'avg_uses'], 'required'],
            [['season_id', 'rule_id', 'special_id', 'sample_size', 'win', 'percentile_5', 'percentile_25', 'percentile_50', 'percentile_75', 'percentile_95', 'percentile_100'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'special_id', 'sample_size', 'win', 'percentile_5', 'percentile_25', 'percentile_50', 'percentile_75', 'percentile_95', 'percentile_100'], 'integer'],
            [['avg_uses', 'stddev'], 'number'],
            [['season_id', 'COALESCE(rule_id, 0)', 'special_id'], 'unique', 'targetAttribute' => ['season_id', 'COALESCE(rule_id, 0)', 'special_id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
            [['special_id'], 'exist', 'skipOnError' => true, 'targetClass' => Special3::class, 'targetAttribute' => ['special_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'special_id' => 'Special ID',
            'sample_size' => 'Sample Size',
            'win' => 'Win',
            'avg_uses' => 'Avg Uses',
            'stddev' => 'Stddev',
            'percentile_5' => 'Percentile 5',
            'percentile_25' => 'Percentile 25',
            'percentile_50' => 'Percentile 50',
            'percentile_75' => 'Percentile 75',
            'percentile_95' => 'Percentile 95',
            'percentile_100' => 'Percentile 100',
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

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special3::class, ['id' => 'special_id']);
    }
}
