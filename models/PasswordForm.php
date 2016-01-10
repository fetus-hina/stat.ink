<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;

class PasswordForm extends Model
{
    public $screen_name;
    public $password;
    public $new_password;
    public $new_password_repeat;

    public function rules()
    {
        return [
            [['screen_name', 'password', 'new_password', 'new_password_repeat'], 'required'],
            [['screen_name'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'screen_name'],
            [['new_password_repeat'], 'compare',
                'compareAttribute' => 'new_password',
                'operator' => '==='],
            [['password'], 'validateOldPassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name'           => Yii::t('app', 'Screen Name (Login Name)'),
            'password'              => Yii::t('app', 'Current Password'),
            'new_password'          => Yii::t('app', 'New Password'),
            'new_password_repeat'   => Yii::t('app', 'New Password (again)'),
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
                Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel('password')])
            );
        }
    }
}
