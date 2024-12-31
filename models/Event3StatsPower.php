<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "event3_stats_power".
 *
 * @property integer $schedule_id
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
 * @property double $p95
 * @property double $maximum
 * @property integer $histogram_width
 * @property double $p80
 *
 * @property EventSchedule3 $schedule
 */
class Event3StatsPower extends ActiveRecord
{
    public static function tableName()
    {
        return 'event3_stats_power';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'users', 'battles', 'agg_battles', 'average'], 'required'],
            [['schedule_id', 'users', 'battles', 'agg_battles', 'histogram_width'], 'default', 'value' => null],
            [['schedule_id', 'users', 'battles', 'agg_battles', 'histogram_width'], 'integer'],
            [['average', 'stddev', 'minimum', 'p05', 'p25', 'p50', 'p75', 'p95', 'maximum', 'p80'], 'number'],
            [['schedule_id'], 'unique'],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'schedule_id' => 'Schedule ID',
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
            'p95' => 'P95',
            'maximum' => 'Maximum',
            'histogram_width' => 'Histogram Width',
            'p80' => 'P80',
        ];
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(EventSchedule3::class, ['id' => 'schedule_id']);
    }
}
