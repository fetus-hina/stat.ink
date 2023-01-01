<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\behaviors\TimestampBehavior;
use app\components\behaviors\UserAuthKeyBehavior;
use app\components\helpers\db\Now;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function password_verify;
use function time;

/**
 * This is the model class for table "user_auth_key".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $auth_key_hint
 * @property string $auth_key_hash
 * @property string $expires_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class UserAuthKey extends ActiveRecord
{
    public const VALID_PERIOD = 30 * 86400;

    public $auth_key_raw;

    public static function find()
    {
        return parent::find()
            ->andWhere(['>=', 'expires_at', new Now()]);
    }

    public static function tableName()
    {
        return 'user_auth_key';
    }

    public static function raw2hint(string $authKey): string
    {
        return UserAuthKeyBehavior::raw2hint($authKey);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            UserAuthKeyBehavior::class,
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    static::EVENT_BEFORE_VALIDATE => 'expires_at',
                ],
                'value' => fn ($event): string => (new DateTimeImmutable())
                        ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
                        ->setTimestamp(time() + static::VALID_PERIOD)
                        ->format(DateTime::ATOM),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['expires_at', 'created_at', 'updated_at'], 'safe'],
            [['auth_key_hint'], 'string', 'max' => 8],
            [['auth_key_hash'], 'string', 'max' => 255],
            [['user_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'auth_key_hint' => 'Auth Key Hint',
            'auth_key_hash' => 'Auth Key Hash',
            'expires_at' => 'Expires At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function validateHash(string $rawKey): bool
    {
        Yii::beginProfile($this->auth_key_hash, __METHOD__);
        $result = !!password_verify($rawKey, $this->auth_key_hash);
        Yii::endProfile($this->auth_key_hash, __METHOD__);
        return $result;
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
