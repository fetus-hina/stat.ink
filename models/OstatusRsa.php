<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use app\components\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ostatus_rsa".
 *
 * @property integer $user_id
 * @property integer $bits
 * @property string $privkey
 * @property string $pubkey
 * @property string $modulus
 * @property string $exponent
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class OstatusRsa extends ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ostatus_rsa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'bits', 'privkey', 'pubkey', 'modulus', 'exponent'], 'required'],
            [['user_id', 'bits'], 'integer'],
            [['privkey', 'pubkey', 'modulus', 'exponent'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
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
            'user_id' => 'User ID',
            'bits' => 'Bits',
            'privkey' => 'Private Key',
            'pubkey' => 'Public Key',
            'modulus' => 'Modulus',
            'exponent' => 'Exponent',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
