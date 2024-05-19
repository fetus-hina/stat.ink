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
 * This is the model class for table "stat_salmon3_tide_event".
 *
 * @property integer $stage_id
 * @property integer $big_stage_id
 * @property integer $tide_id
 * @property integer $event_id
 * @property integer $jobs
 * @property integer $cleared
 *
 * @property Map3 $bigStage
 * @property SalmonEvent3 $event
 * @property SalmonMap3 $stage
 * @property SalmonWaterLevel2 $tide
 */
class StatSalmon3TideEvent extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_salmon3_tide_event';
    }

    public function rules()
    {
        return [
            [['stage_id', 'big_stage_id', 'tide_id', 'event_id', 'jobs', 'cleared'], 'default', 'value' => null],
            [['stage_id', 'big_stage_id', 'tide_id', 'event_id', 'jobs', 'cleared'], 'integer'],
            [['tide_id', 'jobs', 'cleared'], 'required'],
            [['big_stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => Map3::class, 'targetAttribute' => ['big_stage_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonEvent3::class, 'targetAttribute' => ['event_id' => 'id']],
            [['stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap3::class, 'targetAttribute' => ['stage_id' => 'id']],
            [['tide_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonWaterLevel2::class, 'targetAttribute' => ['tide_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'stage_id' => 'Stage ID',
            'big_stage_id' => 'Big Stage ID',
            'tide_id' => 'Tide ID',
            'event_id' => 'Event ID',
            'jobs' => 'Jobs',
            'cleared' => 'Cleared',
        ];
    }

    public function getBigStage(): ActiveQuery
    {
        return $this->hasOne(Map3::class, ['id' => 'big_stage_id']);
    }

    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(SalmonEvent3::class, ['id' => 'event_id']);
    }

    public function getStage(): ActiveQuery
    {
        return $this->hasOne(SalmonMap3::class, ['id' => 'stage_id']);
    }

    public function getTide(): ActiveQuery
    {
        return $this->hasOne(SalmonWaterLevel2::class, ['id' => 'tide_id']);
    }
}
