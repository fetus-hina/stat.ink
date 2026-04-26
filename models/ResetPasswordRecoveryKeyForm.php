<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use DateTime;
use RuntimeException;
use Throwable;
use Yii;
use app\components\helpers\Password;
use app\components\helpers\TypeHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

use function date;
use function preg_match;
use function strtolower;

final class ResetPasswordRecoveryKeyForm extends Model
{
    public const RECOVERY_KEY_PATTERN =
        '/^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})\.([A-Za-z0-9_-]{43})$/';

    public string|null $screen_name = null;
    public string|null $recovery_key = null;
    public string|null $password = null;
    public string|null $password_repeat = null;
    public string|null $cf_turnstile_response = null;

    private UserPasswordRecoveryKey|false|null $foundKey = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['screen_name', 'recovery_key', 'password', 'password_repeat'], 'required'],
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

            [['recovery_key'], 'string'],
            [['recovery_key'], 'match', 'pattern' => self::RECOVERY_KEY_PATTERN],
            [['recovery_key'], 'validateRecoveryKey'],

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
            'recovery_key' => Yii::t('app-recovery-key', 'Recovery Key'),
            'password' => Yii::t('app', 'New Password'),
            'password_repeat' => Yii::t('app', 'New Password (again)'),
        ];
    }

    public function validateRecoveryKey(string $attribute, mixed $params): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if (!preg_match(self::RECOVERY_KEY_PATTERN, (string)$this->recovery_key, $m)) {
            $this->addInvalidError($attribute);
            return;
        }

        $publicId = strtolower($m[1]);
        $secret = $m[2];

        $key = UserPasswordRecoveryKey::find()
            ->andWhere([
                'public_id' => $publicId,
                'used_at' => null,
                'revoked_at' => null,
            ])
            ->limit(1)
            ->one();

        $isValid = false;
        if ($key && $key->user && $key->user->screen_name === (string)$this->screen_name) {
            $isValid = Password::verify($secret, $key->secret_hash);
        } else {
            // Mitigate timing attacks by always running the slow verify
            Password::verify($secret, self::dummyHash());
        }

        if (!$isValid) {
            $this->addInvalidError($attribute);
            return;
        }

        $this->foundKey = $key;
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
        if ($this->hasErrors() || !$this->foundKey) {
            return false;
        }

        $key = $this->foundKey;
        $user = $key->user;
        if (!$user) {
            return false;
        }

        try {
            Yii::$app->db->transaction(function () use ($key, $user): void {
                $req = Yii::$app->request;
                $now = date(
                    DateTime::ATOM,
                    TypeHelper::int($_SERVER['REQUEST_TIME']),
                );

                $key->used_at = $now;
                $key->used_ip = $req->userIP;
                if (!$key->save()) {
                    throw new RuntimeException('Failed to mark recovery key as used');
                }

                if (!$user->changePassword((string)$this->password)) {
                    throw new RuntimeException('Failed to change password');
                }
            });
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
            $this->addError('password', 'Could not update password.');
            return false;
        }

        return true;
    }

    private function addInvalidError(string $attribute): void
    {
        $this->addError(
            $attribute,
            Yii::t('app', 'Invalid {0} or {1}.', [
                $this->getAttributeLabel('screen_name'),
                $this->getAttributeLabel('recovery_key'),
            ]),
        );
    }

    private static function dummyHash(): string
    {
        static $hash = null;
        return $hash ??= Password::hash('dummy-recovery-key-for-timing-equalization');
    }
}
