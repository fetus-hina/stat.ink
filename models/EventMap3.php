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
 * This is the model class for table "event_map3".
 *
 * @property integer $id
 * @property integer $schedule_id
 * @property integer $map_id
 *
 * @property Map3 $map
 * @property EventSchedule3 $schedule
 */
class EventMap3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'event_map3';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'map_id'], 'required'],
            [['schedule_id', 'map_id'], 'default', 'value' => null],
            [['schedule_id', 'map_id'], 'integer'],
            [['schedule_id', 'map_id'], 'unique', 'targetAttribute' => ['schedule_id', 'map_id']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => Map3::class, 'targetAttribute' => ['map_id' => 'id']],
        ];
    }

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
        return $this->hasOne(EventSchedule3::class, ['id' => 'schedule_id']);
    }
}
