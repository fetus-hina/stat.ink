<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_weapon3".
 *
 * @property integer $user_id
 * @property integer $weapon_id
 * @property integer $battles
 * @property string $last_used_at
 *
 * @property User $user
 * @property Weapon3 $weapon
 */
class UserWeapon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_weapon3';
    }

    public function rules()
    {
        return [
            [['user_id', 'weapon_id', 'battles', 'last_used_at'], 'required'],
            [['user_id', 'weapon_id', 'battles'], 'default', 'value' => null],
            [['user_id', 'weapon_id', 'battles'], 'integer'],
            [['last_used_at'], 'safe'],
            [['user_id', 'weapon_id'], 'unique', 'targetAttribute' => ['user_id', 'weapon_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'weapon_id' => 'Weapon ID',
            'battles' => 'Battles',
            'last_used_at' => 'Last Used At',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon3::class, ['id' => 'weapon_id']);
    }
}
