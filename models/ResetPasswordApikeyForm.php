<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Throwable;
use Yii;
use app\components\helpers\Password;
use app\components\helpers\TypeHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

final class ResetPasswordApikeyForm extends Model
{
    public string|null $screen_name = null;
    public string|null $api_key = null;
    public string|null $password = null;
    public string|null $password_repeat = null;
    public string|null $cf_turnstile_response = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['screen_name', 'api_key', 'password', 'password_repeat'], 'required'],
            [['cf_turnstile_response'], 'required',
                'enableClientValidation' => false,
            ],
            [['screen_name'], 'string', 'max' => 15],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                'message' => Yii::t(
                    'app',
                    '{attribute} must be at most 15 alphanumeric or underscore characters.',
                ),
            ],

            [['cf_turnstile_response'], 'string'],
            [['cf_turnstile_response'], 'validateTurnstile'],

            [['api_key'], 'string', 'length' => 43],
            [['api_key'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]{43}$/'],
            [['api_key'], 'validateApiKey'],

            [['password'], 'string', 'min' => 10],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name' => Yii::t('app', 'Screen Name (Login Name)'),
            'api_key' => Yii::t('app', 'API Token'),
            'password' => Yii::t('app', 'New Password'),
            'password_repeat' => Yii::t('app', 'New Password (again)'),
        ];
    }

    public function validateApiKey(string $attribute, mixed $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if (
            !$user ||
            $user->api_key !== $this->api_key
        ) {
            $this->addError(
                $attribute,
                Yii::t('app', 'Invalid {0} or {1}.', [
                    $this->getAttributeLabel('screen_name'),
                    $this->getAttributeLabel('api_key'),
                ]),
            );
        }
    }

    public function validateTurnstile(string $attribute, mixed $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $cfData = [
            'secret' => TypeHelper::string(
                ArrayHelper::getValue(Yii::$app->params, 'cloudflareTurnstile.secretKey'),
            ),
            'response' => $this->cf_turnstile_response,
            'remoteip' => TypeHelper::string(
                ArrayHelper::getValue(Yii::$app->request->headers, 'CF-Connecting-IP'),
            ),
        ];

        try {
            $client = Yii::createObject(Client::class);
            $client->setTransport(CurlTransport::class);
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('https://challenges.cloudflare.com/turnstile/v0/siteverify')
                ->setData($cfData)
                ->send();
            if ($response->isOk) {
                $isSuccess = ArrayHelper::getValue(TypeHelper::array($response->data), 'success');
                if ($isSuccess) {
                    return;
                }
            }
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
        }

        $this->addError(
            $attribute,
            Yii::t('app', 'Failed to validate CAPTCHA.'),
        );
    }

    public function updatePassword(): bool
    {
        if ($this->hasErrors()) {
            return false;
        }

        $user = $this->getUser();
        $user->password = Password::hash($this->password);
        $user->apikey_password_reset = false;
        if (!$user->save()) {
            $this->addError('password', 'Could not update password.');
            return false;
        }

        return true;
    }

    private User|false|null $user = false;

    private function getUser(): ?User
    {
        if ($this->user === false) {
            $this->user = User::find()
                ->andWhere([
                    '{{user}}.[[screen_name]]' => (string)$this->screen_name,
                    '{{user}}.[[apikey_password_reset]]' => true,
                ])
                ->limit(1)
                ->one();
        }

        return $this->user;
    }

    public function sendEmail(): void
    {
        $user = $this->getUser();
        if (!$user || !$user->email) {
            return;
        }

        Yii::$app->mailer
            ->compose(
                ['text' => '@app/views/email/change-password'],
                ['user' => $user],
            )
            ->setFrom(Yii::$app->params['notifyEmail'])
            ->setTo([$user->email => $user->name])
            ->setSubject(Yii::t(
                'app-email',
                '[{site}] {name} (@{screen_name}): Changed your password',
                [
                    'name' => $user->name,
                    'screen_name' => $user->screen_name,
                    'site' => Yii::$app->name,
                ],
                $user->emailLang->lang ?? 'en-US',
            ))
            ->send();
    }
}
