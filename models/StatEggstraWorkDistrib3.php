<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_eggstra_work_distrib3".
 *
 * @property integer $schedule_id
 * @property integer $golden_egg
 * @property integer $users
 *
 * @property SalmonSchedule3 $schedule
 */
class StatEggstraWorkDistrib3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_eggstra_work_distrib3';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'golden_egg', 'users'], 'required'],
            [['schedule_id', 'golden_egg', 'users'], 'default', 'value' => null],
            [['schedule_id', 'golden_egg', 'users'], 'integer'],
            [['schedule_id', 'golden_egg'], 'unique', 'targetAttribute' => ['schedule_id', 'golden_egg']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'schedule_id' => 'Schedule ID',
            'golden_egg' => 'Golden Egg',
            'users' => 'Users',
        ];
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(SalmonSchedule3::class, ['id' => 'schedule_id']);
    }
}
