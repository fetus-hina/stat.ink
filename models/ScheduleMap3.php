<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "schedule_map3".
 *
 * @property integer $id
 * @property integer $schedule_id
 * @property integer $map_id
 *
 * @property Map3 $map
 * @property Schedule3 $schedule
 */
class ScheduleMap3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'schedule_map3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['map_id'], 'default', 'value' => null],
            [['schedule_id'], 'required'],
            [['schedule_id', 'map_id'], 'default', 'value' => null],
            [['schedule_id', 'map_id'], 'integer'],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => Map3::class, 'targetAttribute' => ['map_id' => 'id']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schedule_id' => 'Schedule ID',
            'map_id' => 'Map ID',
        ];
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(Map3::class, ['id' => 'map_id']);
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(Schedule3::class, ['id' => 'schedule_id']);
    }
}
