<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use Yii;
use yii\base\Model;

use function headers_sent;

final class LoginForm extends Model
{
    /**
     * @var string|null
     */
    public $screen_name;
    /**
     * @var string|null
     */
    public $password;
    /**
     * @var bool|int|string|null
     */
    public $remember_me;

    private User|false|null $user = false;

    public function rules()
    {
        return [
            [['screen_name', 'password'], 'required'],
            [['screen_name'], 'string', 'max' => 15],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                'message' => Yii::t(
                    'app-user',
                    '{attribute} must be at most 15 alphanumeric or underscore characters.',
                ),
            ],
            [['password'], 'validatePassword'],
            [['remember_me'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name' => Yii::t('app-user', 'Screen Name (Login Name)'),
            'password' => Yii::t('app', 'Password'),
            'remember_me' => Yii::t('app-user', 'Remember me'),
        ];
    }

    public function validatePassword(string $attribute, mixed $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError(
                $attribute,
                Yii::t('app-user', 'Invalid {0} or {1}.', [
                    $this->getAttributeLabel('screen_name'),
                    $this->getAttributeLabel('password'),
                ]),
            );
        }
    }

    public function login(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if (!$user = $this->getUser()) {
            return false;
        }

        if ($user->rehashPasswordIfNeeded($this->password)) {
            $user->save();
        }

        $appUser = Yii::$app->user;
        $appUser->on(\yii\web\User::EVENT_AFTER_LOGIN, function ($event) use ($user): void {
            UserLoginHistory::login($user, LoginMethod::METHOD_PASSWORD);
            User::onLogin($user, LoginMethod::METHOD_PASSWORD);
        });

        if (!headers_sent()) {
            Yii::$app->session->regenerateID(true);
        }

        return $appUser->login(
            $user,
            $this->remember_me
                ? UserAuthKey::VALID_PERIOD
                : 0,
        );
    }

    public function getUser(): ?User
    {
        if ($this->user === false) {
            $this->user = User::find()
                ->andWhere(['[[screen_name]]' => (string)$this->screen_name])
                ->limit(1)
                ->one();
        }
        return $this->user;
    }
}
