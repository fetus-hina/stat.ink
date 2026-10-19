<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use RuntimeException;
use Yii;
use app\components\behaviors\TimestampBehavior;
use app\components\helpers\WebAuthnHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

use function implode;
use function is_array;
use function random_bytes;
use function vsprintf;

/**
 * This is the model class for table "passkey_challenge".
 *
 * Actions should use PasskeyChallengeStore, which keeps the token in the
 * session.
 *
 * @property string $token
 * @property string $purpose
 * @property integer|null $user_id
 * @property string $challenge
 * @property array<string, mixed>|null $payload
 * @property-read string $expires_at
 * @property-write string|Expression $expires_at
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

    /**
     * Seconds a challenge stays valid (the browser-side timeout is 60 seconds)
     */
    public const TTL = 300;

    public static function tableName()
    {
        return 'passkey_challenge';
    }

    /**
     * Stores a new challenge
     *
     * @param string $challenge the raw (binary) challenge
     * @param int|null $userId the user the challenge is bound to, if logged in
     * @param array<string, mixed>|null $payload data to restore on take()
     */
    public static function create(
        string $purpose,
        string $challenge,
        ?int $userId,
        ?array $payload = null,
    ): self {
        $model = Yii::createObject([
            'class' => self::class,
            'token' => WebAuthnHelper::base64UrlEncode(random_bytes(32)),
            'purpose' => $purpose,
            'user_id' => $userId,
            'challenge' => WebAuthnHelper::base64UrlEncode($challenge),
            'payload' => $payload,
            'expires_at' => new Expression(
                'NOW() + CAST(:ttl AS INTEGER) * INTERVAL \'1 second\'',
                [':ttl' => self::TTL],
            ),
        ]);
        if (!$model->save()) {
            throw new RuntimeException('Failed to store the passkey challenge');
        }

        return $model;
    }

    /**
     * Deletes and returns the challenge; only one caller can take it
     *
     * Returns null if there is no such challenge, it has expired, or it was
     * issued for another purpose or user.
     */
    public static function take(string $token, string $purpose, ?int $userId): ?self
    {
        $db = static::getDb();
        $row = $db
            ->createCommand(
                vsprintf('DELETE FROM %s WHERE %s RETURNING *', [
                    $db->quoteTableName(static::tableName()),
                    implode(' AND ', [
                        '[[token]] = :token',
                        '[[purpose]] = :purpose',
                        '[[user_id]] IS NOT DISTINCT FROM CAST(:user_id AS INTEGER)',
                        '[[expires_at]] > NOW()',
                    ]),
                ]),
                [
                    ':token' => $token,
                    ':purpose' => $purpose,
                    ':user_id' => $userId,
                ],
            )
            ->queryOne();
        if (!is_array($row)) {
            return null;
        }

        $model = static::instantiate($row);
        static::populateRecord($model, $row);
        return $model;
    }

    public static function deleteExpired(): void
    {
        static::deleteAll(['<=', 'expires_at', new Expression('NOW()')]);
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
