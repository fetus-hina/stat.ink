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
 * This is the model class for table "user_stat_eggstra_work3".
 *
 * @property integer $user_id
 * @property integer $schedule_id
 * @property integer $golden_eggs
 *
 * @property SalmonSchedule3 $schedule
 * @property User $user
 */
class UserStatEggstraWork3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_stat_eggstra_work3';
    }

    public function rules()
    {
        return [
            [['user_id', 'schedule_id', 'golden_eggs'], 'required'],
            [['user_id', 'schedule_id', 'golden_eggs'], 'default', 'value' => null],
            [['user_id', 'schedule_id', 'golden_eggs'], 'integer'],
            [['user_id', 'schedule_id'], 'unique', 'targetAttribute' => ['user_id', 'schedule_id']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'schedule_id' => 'Schedule ID',
            'golden_eggs' => 'Golden Eggs',
        ];
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(SalmonSchedule3::class, ['id' => 'schedule_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
