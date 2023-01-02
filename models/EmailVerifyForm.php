<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class EmailVerifyForm extends Model
{
    public $email;
    public $verifyCode;
    public $validVerifyCode;

    public function rules()
    {
        return [
            [['email', 'verifyCode'], 'trim'],
            [['email'], 'validateHashedEmail'],
            [['verifyCode'], 'match',
                'pattern' => '/^[a-z2-7]{5}$/',
            ],
            [['verifyCode'], 'compare',
                'skipOnError' => true,
                'compareAttribute' => 'validVerifyCode',
                'enableClientValidation' => false,
                'operator' => '===',
                'message' => Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => Yii::t('app', 'Verification Code'),
                ]),
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'verifyCode' => Yii::t('app', 'Verification Code'),
        ];
    }

    public function validateHashedEmail(string $attribute, $params): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if ($this->getRealEmail() === null) {
            $this->addError($attribute, 'Form data broken. Back to profile and try again.');
        }
    }

    public function setRealEmail(string $email): self
    {
        $this->email = Yii::$app->security->hashData(
            $email,
            Yii::$app->request->cookieValidationKey,
        );

        return $this;
    }

    public function getRealEmail(): ?string
    {
        $result = Yii::$app->security->validateData(
            $this->email,
            Yii::$app->request->cookieValidationKey,
        );

        return $result === false ? null : $result;
    }
}
