<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_weapon2".
 *
 * @property integer $id
 * @property integer $schedule_id
 * @property integer $weapon_id
 *
 * @property SalmonSchedule2 $schedule
 * @property Weapon2 $weapon
 */
class SalmonWeapon2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_weapon2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schedule_id'], 'required'],
            [['schedule_id', 'weapon_id'], 'default', 'value' => null],
            [['schedule_id', 'weapon_id'], 'integer'],
            [['schedule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonSchedule2::class,
                'targetAttribute' => ['schedule_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schedule_id' => 'Schedule ID',
            'weapon_id' => 'Weapon ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchedule()
    {
        return $this->hasOne(SalmonSchedule2::class, ['id' => 'schedule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }
}
