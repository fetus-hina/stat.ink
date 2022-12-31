<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatnet2_user_map".
 *
 * @property string $splatnet_id
 * @property integer $user_id
 * @property integer $battles
 *
 * @property User $user
 */
class Splatnet2UserMap extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatnet2_user_map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['splatnet_id', 'user_id', 'battles'], 'required'],
            [['user_id', 'battles'], 'integer'],
            [['splatnet_id'], 'string', 'max' => 16],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'splatnet_id' => 'Splatnet ID',
            'user_id' => 'User ID',
            'battles' => 'Battles',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
