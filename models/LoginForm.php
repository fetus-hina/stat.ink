<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $screen_name;
    public $password;
    private $user = false;

    public function rules()
    {
        $rules = [
            [['screen_name', 'password'], 'required'],
            [['screen_name'], 'string', 'max' => 15],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                'message' => Yii::t(
                    'app',
                    '{attribute} must be at most 15 alphanumeric or underscore characters.'
                ),
            ],
            [['password'], 'validatePassword'],
        ];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name'       => Yii::t('app', 'Screen Name (Login Name)'),
            'password'          => Yii::t('app', 'Password'),
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }
        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError(
                $attribute,
                Yii::t('app', 'Invalid {0} or {1}.', [
                    $this->getAttributeLabel('screen_name'),
                    $this->getAttributeLabel('password'),
                ])
            );
        }
    }

    public function login()
    {
        if (!$this->validate()) {
            return false;
        }
        $user = $this->getUser();
        if ($user->rehashPasswordIfNeeded($this->password)) {
            $user->save();
        }
        return Yii::$app->user->login($user, 0);
    }

    public function getUser()
    {
        if ($this->user === false) {
            $this->user = User::findOne(['[[screen_name]]' => $this->screen_name]);
        }
        return $this->user;
    }
}
