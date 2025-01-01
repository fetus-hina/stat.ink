<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon3_user_stats_event".
 *
 * @property integer $user_id
 * @property integer $map_id
 * @property integer $tide_id
 * @property integer $event_id
 * @property integer $waves
 * @property integer $cleared
 * @property integer $total_quota
 * @property integer $total_delivered
 *
 * @property SalmonEvent3 $event
 * @property SalmonMap3 $map
 * @property SalmonWaterLevel2 $tide
 * @property User $user
 */
class Salmon3UserStatsEvent extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon3_user_stats_event';
    }

    public function rules()
    {
        return [
            [['user_id', 'map_id', 'tide_id', 'waves', 'cleared', 'total_quota', 'total_delivered'], 'required'],
            [['user_id', 'map_id', 'tide_id', 'event_id', 'waves', 'cleared', 'total_quota', 'total_delivered'], 'default', 'value' => null],
            [['user_id', 'map_id', 'tide_id', 'event_id', 'waves', 'cleared', 'total_quota', 'total_delivered'], 'integer'],
            [['user_id', 'map_id', 'tide_id'], 'unique', 'targetAttribute' => ['user_id', 'map_id', 'tide_id']],
            [['user_id', 'map_id', 'tide_id', 'event_id'], 'unique', 'targetAttribute' => ['user_id', 'map_id', 'tide_id', 'event_id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonEvent3::class, 'targetAttribute' => ['event_id' => 'id']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap3::class, 'targetAttribute' => ['map_id' => 'id']],
            [['tide_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonWaterLevel2::class, 'targetAttribute' => ['tide_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'map_id' => 'Map ID',
            'tide_id' => 'Tide ID',
            'event_id' => 'Event ID',
            'waves' => 'Waves',
            'cleared' => 'Cleared',
            'total_quota' => 'Total Quota',
            'total_delivered' => 'Total Delivered',
        ];
    }

    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(SalmonEvent3::class, ['id' => 'event_id']);
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(SalmonMap3::class, ['id' => 'map_id']);
    }

    public function getTide(): ActiveQuery
    {
        return $this->hasOne(SalmonWaterLevel2::class, ['id' => 'tide_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
