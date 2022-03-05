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
 * This is the model class for table "stat_salmon2_weapon_clear_rate".
 *
 * @property int $stage_id
 * @property int $weapon_id
 * @property int $plays
 * @property int $cleared
 * @property string $last_data_at
 *
 * @property SalmonMap2 $stage
 * @property Weapon2 $weapon
 */
class StatSalmon2WeaponClearRate extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_salmon2_weapon_clear_rate';
    }

    public function rules()
    {
        return [
            [['stage_id', 'weapon_id', 'plays', 'cleared', 'last_data_at'], 'required'],
            [['stage_id', 'weapon_id', 'plays', 'cleared'], 'default', 'value' => null],
            [['stage_id', 'weapon_id', 'plays', 'cleared'], 'integer'],
            [['last_data_at'], 'safe'],
            [['stage_id', 'weapon_id'], 'unique',
                'targetAttribute' => ['stage_id', 'weapon_id'],
            ],
            [['stage_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => ['stage_id' => 'id'],
            ],
            [['weapon_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'stage_id' => 'Stage ID',
            'weapon_id' => 'Weapon ID',
            'plays' => 'Plays',
            'cleared' => 'Cleared',
            'last_data_at' => 'Last Data At',
        ];
    }

    public function getStage(): ActiveQuery
    {
        return $this->hasOne(SalmonMap2::class, ['id' => 'stage_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }
}
