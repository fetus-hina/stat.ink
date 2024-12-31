<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use const SORT_DESC;

/**
 * This is the model class for table "user_weapon2".
 *
 * @property integer $user_id
 * @property integer $weapon_id
 * @property integer $battles
 * @property string $last_used_at
 *
 * @property User $user
 * @property Weapon2 $weapon
 */
class UserWeapon2 extends ActiveRecord
{
    public static function find()
    {
        return new class (static::class) extends ActiveQuery {
            public function favoriteOrder(): self
            {
                return $this
                    ->andWhere(['>', 'battles', 0])
                    ->orderBy(['battles' => SORT_DESC]);
            }
        };
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_weapon2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'weapon_id', 'battles', 'last_used_at'], 'required'],
            [['user_id', 'weapon_id', 'battles'], 'integer'],
            [['last_used_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
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
            'user_id' => 'User ID',
            'weapon_id' => 'Weapon ID',
            'battles' => 'Battles',
            'last_used_at' => 'Last Used At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }
}
