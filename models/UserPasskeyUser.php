<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_passkey_user".
 *
 * @property integer $user_id
 * @property string $user_handle
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property UserPasskey[] $userPasskeys
 */
class UserPasskeyUser extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_passkey_user';
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'user_handle', 'created_at', 'updated_at'], 'required'],
            [['user_id'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_handle'], 'string', 'max' => 128],
            [['user_handle'], 'unique'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_handle' => 'User Handle',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getUserPasskeys(): ActiveQuery
    {
        return $this->hasMany(UserPasskey::class, ['user_id' => 'user_id']);
    }
}
