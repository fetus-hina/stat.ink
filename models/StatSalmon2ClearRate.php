<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_salmon2_clear_rate".
 *
 * @property integer $stage_id
 * @property integer $plays
 * @property string $avg_clear_waves
 * @property string $sd_clear_waves
 * @property integer $cleared
 * @property integer $fail_wave1
 * @property integer $fail_wave2
 * @property integer $fail_wave3
 * @property integer $fail_wiped
 * @property integer $fail_timed
 * @property string $avg_golden_eggs
 * @property string $sd_golden_eggs
 * @property string $avg_power_eggs
 * @property string $sd_power_eggs
 * @property string $avg_deaths
 * @property string $sd_deaths
 * @property string $last_data_at
 *
 * @property SalmonMap2 $stage
 */
class StatSalmon2ClearRate extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_salmon2_clear_rate';
    }

    public function rules()
    {
        return [
            [['stage_id', 'plays', 'avg_clear_waves', 'sd_clear_waves', 'cleared'], 'required'],
            [['fail_wave1', 'fail_wave2', 'fail_wave3', 'fail_wiped', 'fail_timed'], 'required'],
            [['last_data_at'], 'required'],
            [['stage_id', 'plays', 'cleared', 'fail_wave1', 'fail_wave2', 'fail_wave3'], 'default',
                'value' => null,
            ],
            [['fail_wiped', 'fail_timed'], 'default',
                'value' => null,
            ],
            [['stage_id', 'plays', 'cleared', 'fail_wave1', 'fail_wave2', 'fail_wave3'], 'integer'],
            [['fail_wiped', 'fail_timed'], 'integer'],
            [['avg_clear_waves', 'sd_clear_waves', 'avg_golden_eggs', 'sd_golden_eggs'], 'number'],
            [['avg_power_eggs', 'sd_power_eggs', 'avg_deaths', 'sd_deaths'], 'number'],
            [['last_data_at'], 'safe'],
            [['stage_id'], 'unique'],
            [['stage_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => ['stage_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'stage_id' => 'Stage ID',
            'plays' => 'Plays',
            'avg_clear_waves' => 'Avg Clear Waves',
            'sd_clear_waves' => 'Sd Clear Waves',
            'cleared' => 'Cleared',
            'fail_wave1' => 'Fail Wave1',
            'fail_wave2' => 'Fail Wave2',
            'fail_wave3' => 'Fail Wave3',
            'fail_wiped' => 'Fail Wiped',
            'fail_timed' => 'Fail Timed',
            'avg_golden_eggs' => 'Avg Golden Eggs',
            'sd_golden_eggs' => 'Sd Golden Eggs',
            'avg_power_eggs' => 'Avg Power Eggs',
            'sd_power_eggs' => 'Sd Power Eggs',
            'avg_deaths' => 'Avg Deaths',
            'sd_deaths' => 'Sd Deaths',
            'last_data_at' => 'Last Data At',
        ];
    }

    public function getStage(): ActiveQuery
    {
        return $this->hasOne(SalmonMap2::class, ['id' => 'stage_id']);
    }

    public function getWeaponStats(): ActiveQuery
    {
        return $this->hasMany(StatSalmon2WeaponClearRate::class, ['stage_id' => 'stage_id']);
    }
}
