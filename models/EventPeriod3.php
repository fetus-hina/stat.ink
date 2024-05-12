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
 * This is the model class for table "event_period3".
 *
 * @property integer $id
 * @property integer $schedule_id
 * @property string $start_at
 * @property string $end_at
 *
 * @property Event3StatsPowerPeriod $event3StatsPowerPeriod
 * @property Event3StatsPowerPeriodHistogram[] $event3StatsPowerPeriodHistograms
 * @property EventSchedule3 $schedule
 */
class EventPeriod3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'event_period3';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'start_at', 'end_at'], 'required'],
            [['schedule_id'], 'default', 'value' => null],
            [['schedule_id'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
            [['schedule_id', 'start_at'], 'unique', 'targetAttribute' => ['schedule_id', 'start_at']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schedule_id' => 'Schedule ID',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }

    public function getEvent3StatsPowerPeriod(): ActiveQuery
    {
        return $this->hasOne(Event3StatsPowerPeriod::class, ['period_id' => 'id']);
    }

    public function getEvent3StatsPowerPeriodHistograms(): ActiveQuery
    {
        return $this->hasMany(Event3StatsPowerPeriodHistogram::class, ['period_id' => 'id']);
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(EventSchedule3::class, ['id' => 'schedule_id']);
    }
}
