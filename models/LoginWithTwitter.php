<?php

/**
 * @copyright Copyright (C) 2016-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "login_with_twitter".
 *
 * @property integer $user_id
 * @property integer $twitter_id
 * @property string $screen_name
 * @property string $name
 *
 * @property User $user
 */
final class LoginWithTwitter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'login_with_twitter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'twitter_id', 'screen_name', 'name'], 'trim'],
            [['name'], 'filter',
                'filter' => function ($value) {
                    $value = trim(mb_substr($value, 0, 32, 'UTF-8'));
                    return $value == ''
                        ? sprintf('@%s', $this->screen_name)
                        : $value;
                },
            ],

            [['user_id', 'twitter_id', 'screen_name', 'name'], 'required'],
            [['user_id'], 'integer'],
            [['twitter_id'], 'match', 'pattern' => '/^[0-9]+$/'],
            [['screen_name'], 'match', 'pattern' => '/^[a-zA-Z0-9_]{1,15}$/'],
            [['name'], 'string', 'max' => 32],
            [['twitter_id'], 'unique'],
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
            'twitter_id' => 'Twitter ID',
            'screen_name' => 'Screen Name',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
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
            UserLoginHistory::login($user, LoginMethod::METHOD_TWITTER);
            User::onLogin($user, LoginMethod::METHOD_TWITTER);
        });

        if (!headers_sent()) {
            Yii::$app->session->regenerateID(true);
        }

        return $appUser->login($user, 0);
    }
}
