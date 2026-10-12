<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use Yii;
use app\components\behaviors\TimestampBehavior;
use app\components\helpers\WebAuthnHelper;
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

    /**
     * Returns the WebAuthn user entity of the user, creating it if it does not exist yet
     */
    public static function findOrCreate(int $userId): ?self
    {
        $model = self::findOne(['user_id' => $userId]);
        if ($model) {
            return $model;
        }

        $model = Yii::createObject([
            'class' => self::class,
            'user_id' => $userId,
            'user_handle' => WebAuthnHelper::generateUserHandleBase64(),
        ]);
        return $model->save() ? $model : null;
    }

    #[Override]
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'user_handle'], 'required'],
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
