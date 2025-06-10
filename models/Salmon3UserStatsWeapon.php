<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
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
 * This is the model class for table "salmon3_user_stats_weapon".
 *
 * @property integer $user_id
 * @property integer $weapon_id
 * @property integer $total_waves
 * @property integer $normal_waves
 * @property integer $normal_waves_cleared
 * @property integer $xtra_waves
 * @property integer $xtra_waves_cleared
 *
 * @property User $user
 * @property SalmonWeapon3 $weapon
 */
class Salmon3UserStatsWeapon extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon3_user_stats_weapon';
    }

    #[Override]
    public function rules()
    {
        return [
            [['xtra_waves_cleared'], 'default', 'value' => 0],
            [['user_id', 'weapon_id'], 'required'],
            [['user_id', 'weapon_id', 'total_waves', 'normal_waves', 'normal_waves_cleared', 'xtra_waves', 'xtra_waves_cleared'], 'default', 'value' => null],
            [['user_id', 'weapon_id', 'total_waves', 'normal_waves', 'normal_waves_cleared', 'xtra_waves', 'xtra_waves_cleared'], 'integer'],
            [['user_id', 'total_waves', 'normal_waves', 'normal_waves_cleared', 'xtra_waves_cleared', 'weapon_id'], 'unique', 'targetAttribute' => ['user_id', 'total_waves', 'normal_waves', 'normal_waves_cleared', 'xtra_waves_cleared', 'weapon_id']],
            [['user_id', 'weapon_id'], 'unique', 'targetAttribute' => ['user_id', 'weapon_id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonWeapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'weapon_id' => 'Weapon ID',
            'total_waves' => 'Total Waves',
            'normal_waves' => 'Normal Waves',
            'normal_waves_cleared' => 'Normal Waves Cleared',
            'xtra_waves' => 'Xtra Waves',
            'xtra_waves_cleared' => 'Xtra Waves Cleared',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(SalmonWeapon3::class, ['id' => 'weapon_id']);
    }
}
