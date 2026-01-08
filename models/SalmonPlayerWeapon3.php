<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_player_weapon3".
 *
 * @property integer $player_id
 * @property integer $wave
 * @property integer $weapon_id
 *
 * @property SalmonPlayer3 $player
 * @property SalmonWeapon3 $weapon
 */
class SalmonPlayerWeapon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_player_weapon3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['weapon_id'], 'default', 'value' => null],
            [['player_id', 'wave'], 'required'],
            [['player_id', 'wave', 'weapon_id'], 'default', 'value' => null],
            [['player_id', 'wave', 'weapon_id'], 'integer'],
            [['player_id', 'wave'], 'unique', 'targetAttribute' => ['player_id', 'wave']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonPlayer3::class, 'targetAttribute' => ['player_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonWeapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'wave' => 'Wave',
            'weapon_id' => 'Weapon ID',
        ];
    }

    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(SalmonPlayer3::class, ['id' => 'player_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(SalmonWeapon3::class, ['id' => 'weapon_id']);
    }
}
