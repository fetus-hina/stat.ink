<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use Yii;
use yii\base\Model;

class PasswordForm extends Model
{
    /**
     * Sets a new password for a user who has disabled their password.
     * The current password is not asked (they re-authenticate with a passkey).
     */
    public const SCENARIO_SET = 'set';

    public $screen_name;
    public $password;
    public $new_password;
    public $new_password_repeat;

    public function rules()
    {
        return [
            [['screen_name', 'new_password', 'new_password_repeat'], 'required'],
            [['password'], 'required', 'except' => self::SCENARIO_SET],
            [['screen_name'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'screen_name',
            ],
            [['new_password'], 'string', 'min' => 10],
            [['new_password_repeat'], 'compare',
                'compareAttribute' => 'new_password',
                'operator' => '===',
            ],
            [['password'], 'validateOldPassword', 'except' => self::SCENARIO_SET],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name' => Yii::t('app-user', 'Screen Name (Login Name)'),
            'password' => Yii::t('app-user', 'Current Password'),
            'new_password' => Yii::t('app-user', 'New Password'),
            'new_password_repeat' => Yii::t('app-user', 'New Password (again)'),
        ];
    }

    public function validateOldPassword($attribute, $model)
    {
        if ($this->hasErrors()) {
            return;
        }
        $user = User::findOne(['screen_name' => $this->screen_name]);
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError(
                $attribute,
                Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel('password')]),
            );
        }
    }
}
