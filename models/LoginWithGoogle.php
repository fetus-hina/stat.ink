<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

use function headers_sent;

/**
 * This is the model class for table "login_with_google".
 *
 * @property integer $user_id
 * @property string $google_id
 * @property string|null $email
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
final class LoginWithGoogle extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'login_with_google';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('now()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'google_id', 'email', 'name'], 'trim'],

            [['user_id', 'google_id', 'name'], 'required'],
            [['user_id'], 'integer'],
            [['google_id'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]{1,64}$/'],
            [['email'], 'email'],
            [['name'], 'string'],
            [['google_id'], 'unique'],
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
            'google_id' => 'Google ID',
            'email' => 'Email',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function login()
    {
        $user = $this->user;
        if (!$user) {
            return false;
        }

        $appUser = Yii::$app->user;
        $appUser->on(\yii\web\User::EVENT_AFTER_LOGIN, function ($event) use ($user): void {
            UserLoginHistory::login($user, LoginMethod::METHOD_GOOGLE);
            User::onLogin($user, LoginMethod::METHOD_GOOGLE);
        });

        if (!headers_sent()) {
            Yii::$app->session->regenerateID(true);
        }

        return $appUser->login($user, 0);
    }
}
