<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use app\components\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "passkey_challenge".
 *
 * Use PasskeyChallengeStore to issue and take challenges.
 *
 * @property string $token
 * @property string $purpose
 * @property integer|null $user_id
 * @property string $challenge
 * @property array<string, mixed>|null $payload
 * @property string $expires_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User|null $user
 */
class PasskeyChallenge extends ActiveRecord
{
    public const PURPOSE_LOGIN = 'login';
    public const PURPOSE_REAUTH = 'reauth';
    public const PURPOSE_REGISTER = 'register';
    public const PURPOSE_SIGNUP = 'signup';

    public static function tableName()
    {
        return 'passkey_challenge';
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
            [['token', 'purpose', 'challenge', 'expires_at'], 'required'],
            [['token', 'purpose', 'challenge'], 'string'],
            [['purpose'], 'in',
                'range' => [
                    self::PURPOSE_LOGIN,
                    self::PURPOSE_REAUTH,
                    self::PURPOSE_REGISTER,
                    self::PURPOSE_SIGNUP,
                ],
            ],
            [['user_id'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['payload', 'expires_at', 'created_at', 'updated_at'], 'safe'],
            [['token'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'token' => 'Token',
            'purpose' => 'Purpose',
            'user_id' => 'User ID',
            'challenge' => 'Challenge',
            'payload' => 'Payload',
            'expires_at' => 'Expires At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
