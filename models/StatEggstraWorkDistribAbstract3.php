<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_eggstra_work_distrib_abstract3".
 *
 * @property integer $schedule_id
 * @property integer $users
 * @property double $average
 * @property double $stddev
 * @property integer $min
 * @property integer $q1
 * @property integer $median
 * @property integer $q3
 * @property integer $max
 * @property integer $top_5_pct
 * @property integer $top_20_pct
 *
 * @property SalmonSchedule3 $schedule
 */
class StatEggstraWorkDistribAbstract3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_eggstra_work_distrib_abstract3';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'users', 'average'], 'required'],
            [['schedule_id', 'users', 'min', 'q1', 'median', 'q3', 'max', 'top_5_pct', 'top_20_pct'], 'default', 'value' => null],
            [['schedule_id', 'users', 'min', 'q1', 'median', 'q3', 'max', 'top_5_pct', 'top_20_pct'], 'integer'],
            [['average', 'stddev'], 'number'],
            [['schedule_id'], 'unique'],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'schedule_id' => 'Schedule ID',
            'users' => 'Users',
            'average' => 'Average',
            'stddev' => 'Stddev',
            'min' => 'Min',
            'q1' => 'Q1',
            'median' => 'Median',
            'q3' => 'Q3',
            'max' => 'Max',
            'top_5_pct' => 'Top 5 Pct',
            'top_20_pct' => 'Top 20 Pct',
        ];
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(SalmonSchedule3::class, ['id' => 'schedule_id']);
    }
}
