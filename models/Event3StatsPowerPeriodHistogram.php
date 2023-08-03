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
 * This is the model class for table "event3_stats_power_period_histogram".
 *
 * @property integer $period_id
 * @property integer $class_value
 * @property integer $battles
 *
 * @property EventPeriod3 $period
 */
class Event3StatsPowerPeriodHistogram extends ActiveRecord
{
    public static function tableName()
    {
        return 'event3_stats_power_period_histogram';
    }

    public function rules()
    {
        return [
            [['period_id', 'class_value', 'battles'], 'required'],
            [['period_id', 'class_value', 'battles'], 'default', 'value' => null],
            [['period_id', 'class_value', 'battles'], 'integer'],
            [['period_id', 'class_value'], 'unique', 'targetAttribute' => ['period_id', 'class_value']],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventPeriod3::class, 'targetAttribute' => ['period_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'period_id' => 'Period ID',
            'class_value' => 'Class Value',
            'battles' => 'Battles',
        ];
    }

    public function getPeriod(): ActiveQuery
    {
        return $this->hasOne(EventPeriod3::class, ['id' => 'period_id']);
    }
}
