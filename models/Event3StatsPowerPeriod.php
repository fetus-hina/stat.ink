<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "event3_stats_power_period".
 *
 * @property integer $period_id
 * @property integer $users
 * @property integer $battles
 * @property integer $agg_battles
 * @property double $average
 * @property double $stddev
 * @property double $minimum
 * @property double $p05
 * @property double $p25
 * @property double $p50
 * @property double $p75
 * @property double $p80
 * @property double $p95
 * @property double $maximum
 *
 * @property EventPeriod3 $period
 */
class Event3StatsPowerPeriod extends ActiveRecord
{
    public static function tableName()
    {
        return 'event3_stats_power_period';
    }

    #[Override]
    public function rules()
    {
        return [
            [['stddev', 'minimum', 'p05', 'p25', 'p50', 'p75', 'p80', 'p95', 'maximum'], 'default', 'value' => null],
            [['period_id', 'users', 'battles', 'agg_battles', 'average'], 'required'],
            [['period_id', 'users', 'battles', 'agg_battles'], 'default', 'value' => null],
            [['period_id', 'users', 'battles', 'agg_battles'], 'integer'],
            [['average', 'stddev', 'minimum', 'p05', 'p25', 'p50', 'p75', 'p80', 'p95', 'maximum'], 'number'],
            [['period_id'], 'unique'],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventPeriod3::class, 'targetAttribute' => ['period_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'period_id' => 'Period ID',
            'users' => 'Users',
            'battles' => 'Battles',
            'agg_battles' => 'Agg Battles',
            'average' => 'Average',
            'stddev' => 'Stddev',
            'minimum' => 'Minimum',
            'p05' => 'P05',
            'p25' => 'P25',
            'p50' => 'P50',
            'p75' => 'P75',
            'p80' => 'P80',
            'p95' => 'P95',
            'maximum' => 'Maximum',
        ];
    }

    public function getPeriod(): ActiveQuery
    {
        return $this->hasOne(EventPeriod3::class, ['id' => 'period_id']);
    }
}
