<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_schedule_weapon3".
 *
 * @property integer $id
 * @property integer $schedule_id
 * @property integer $weapon_id
 * @property integer $random_id
 *
 * @property SalmonRandom3 $random
 * @property SalmonSchedule3 $schedule
 * @property SalmonWeapon3 $weapon
 */
class SalmonScheduleWeapon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_schedule_weapon3';
    }

    public function rules()
    {
        return [
            [['schedule_id'], 'required'],
            [['schedule_id', 'weapon_id', 'random_id'], 'default', 'value' => null],
            [['schedule_id', 'weapon_id', 'random_id'], 'integer'],
            [['random_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonRandom3::class, 'targetAttribute' => ['random_id' => 'id']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonWeapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schedule_id' => 'Schedule ID',
            'weapon_id' => 'Weapon ID',
            'random_id' => 'Random ID',
        ];
    }

    public function getRandom(): ActiveQuery
    {
        return $this->hasOne(SalmonRandom3::class, ['id' => 'random_id']);
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(SalmonSchedule3::class, ['id' => 'schedule_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(SalmonWeapon3::class, ['id' => 'weapon_id']);
    }
}
