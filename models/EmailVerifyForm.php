<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

use function is_string;

/**
 * @property-read string|null $realEmail
 * @property-write string $realEmail
 */
class EmailVerifyForm extends Model
{
    /** @var string|array<mixed>|null */
    public $email;
    /** @var string|array<mixed>|null */
    public $verifyCode;
    /** @var string|null */
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
                    'attribute' => Yii::t('app-user', 'Verification Code'),
                ]),
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'verifyCode' => Yii::t('app-user', 'Verification Code'),
        ];
    }

    public function validateHashedEmail(string $attribute, mixed $params): void
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
        if (!is_string($this->email)) {
            return null;
        }

        $result = Yii::$app->security->validateData(
            $this->email,
            Yii::$app->request->cookieValidationKey,
        );

        return $result === false ? null : $result;
    }
}
