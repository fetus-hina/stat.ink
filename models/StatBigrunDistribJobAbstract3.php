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
 * This is the model class for table "stat_bigrun_distrib_job_abstract3".
 *
 * @property integer $schedule_id
 * @property integer $users
 * @property integer $jobs
 * @property double $average
 * @property double $stddev
 * @property integer $clear_jobs
 * @property double $clear_average
 * @property double $clear_stddev
 * @property integer $min
 * @property integer $p05
 * @property integer $p25
 * @property integer $p50
 * @property integer $p75
 * @property integer $p80
 * @property integer $p95
 * @property integer $max
 * @property integer $histogram_width
 * @property integer $w1_failed_jobs
 * @property double $w1_failed_average
 * @property double $w1_failed_stddev
 * @property integer $w2_failed_jobs
 * @property double $w2_failed_average
 * @property double $w2_failed_stddev
 * @property integer $w3_failed_jobs
 * @property double $w3_failed_average
 * @property double $w3_failed_stddev
 *
 * @property SalmonSchedule3 $schedule
 */
class StatBigrunDistribJobAbstract3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_bigrun_distrib_job_abstract3';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'users', 'jobs', 'average', 'clear_jobs'], 'required'],
            [['schedule_id', 'users', 'jobs', 'clear_jobs', 'min', 'p05', 'p25', 'p50', 'p75', 'p80', 'p95', 'max', 'histogram_width', 'w1_failed_jobs', 'w2_failed_jobs', 'w3_failed_jobs'], 'default', 'value' => null],
            [['schedule_id', 'users', 'jobs', 'clear_jobs', 'min', 'p05', 'p25', 'p50', 'p75', 'p80', 'p95', 'max', 'histogram_width', 'w1_failed_jobs', 'w2_failed_jobs', 'w3_failed_jobs'], 'integer'],
            [['average', 'stddev', 'clear_average', 'clear_stddev', 'w1_failed_average', 'w1_failed_stddev', 'w2_failed_average', 'w2_failed_stddev', 'w3_failed_average', 'w3_failed_stddev'], 'number'],
            [['schedule_id'], 'unique'],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'schedule_id' => 'Schedule ID',
            'users' => 'Users',
            'jobs' => 'Jobs',
            'average' => 'Average',
            'stddev' => 'Stddev',
            'clear_jobs' => 'Clear Jobs',
            'clear_average' => 'Clear Average',
            'clear_stddev' => 'Clear Stddev',
            'min' => 'Min',
            'p05' => 'P05',
            'p25' => 'P25',
            'p50' => 'P50',
            'p75' => 'P75',
            'p80' => 'P80',
            'p95' => 'P95',
            'max' => 'Max',
            'histogram_width' => 'Histogram Width',
            'w1_failed_jobs' => 'W1 Failed Jobs',
            'w1_failed_average' => 'W1 Failed Average',
            'w1_failed_stddev' => 'W1 Failed Stddev',
            'w2_failed_jobs' => 'W2 Failed Jobs',
            'w2_failed_average' => 'W2 Failed Average',
            'w2_failed_stddev' => 'W2 Failed Stddev',
            'w3_failed_jobs' => 'W3 Failed Jobs',
            'w3_failed_average' => 'W3 Failed Average',
            'w3_failed_stddev' => 'W3 Failed Stddev',
        ];
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(SalmonSchedule3::class, ['id' => 'schedule_id']);
    }
}
