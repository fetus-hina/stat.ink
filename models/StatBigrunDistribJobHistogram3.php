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
 * This is the model class for table "stat_bigrun_distrib_job_histogram3".
 *
 * @property integer $schedule_id
 * @property integer $class_value
 * @property integer $count
 *
 * @property SalmonSchedule3 $schedule
 */
class StatBigrunDistribJobHistogram3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_bigrun_distrib_job_histogram3';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'class_value', 'count'], 'required'],
            [['schedule_id', 'class_value', 'count'], 'default', 'value' => null],
            [['schedule_id', 'class_value', 'count'], 'integer'],
            [['schedule_id', 'class_value'], 'unique', 'targetAttribute' => ['schedule_id', 'class_value']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'schedule_id' => 'Schedule ID',
            'class_value' => 'Class Value',
            'count' => 'Count',
        ];
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(SalmonSchedule3::class, ['id' => 'schedule_id']);
    }
}
