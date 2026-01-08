<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_player_weapon2".
 *
 * @property integer $player_id
 * @property integer $wave
 * @property integer $weapon_id
 *
 * @property SalmonMainWeapon2 $weapon
 * @property SalmonPlayer2 $player
 */
class SalmonPlayerWeapon2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_player_weapon2';
    }

    public function rules()
    {
        return [
            [['player_id', 'wave', 'weapon_id'], 'required'],
            [['player_id', 'wave', 'weapon_id'], 'default', 'value' => null],
            [['player_id', 'wave', 'weapon_id'], 'integer'],
            [['player_id', 'wave'], 'unique', 'targetAttribute' => ['player_id', 'wave']],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonMainWeapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
            [['player_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonPlayer2::class,
                'targetAttribute' => ['player_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'wave' => 'Wave',
            'weapon_id' => 'Weapon ID',
        ];
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(SalmonMainWeapon2::class, ['id' => 'weapon_id']);
    }

    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(SalmonPlayer2::class, ['id' => 'player_id']);
    }
}
