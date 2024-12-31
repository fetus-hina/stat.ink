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
 * This is the model class for table "event3_stats_power_histogram".
 *
 * @property integer $schedule_id
 * @property integer $class_value
 * @property integer $battles
 *
 * @property EventSchedule3 $schedule
 */
class Event3StatsPowerHistogram extends ActiveRecord
{
    public static function tableName()
    {
        return 'event3_stats_power_histogram';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'class_value', 'battles'], 'required'],
            [['schedule_id', 'class_value', 'battles'], 'default', 'value' => null],
            [['schedule_id', 'class_value', 'battles'], 'integer'],
            [['schedule_id', 'class_value'], 'unique', 'targetAttribute' => ['schedule_id', 'class_value']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'schedule_id' => 'Schedule ID',
            'class_value' => 'Class Value',
            'battles' => 'Battles',
        ];
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(EventSchedule3::class, ['id' => 'schedule_id']);
    }
}
