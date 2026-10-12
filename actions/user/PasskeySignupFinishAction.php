<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use RuntimeException;
use Throwable;
use Yii;
use app\components\helpers\WebAuthnHelper;
use app\models\LoginMethod;
use app\models\PasskeyAttestationForm;
use app\models\RegisterForm;
use app\models\User;
use app\models\UserLoginHistory;
use app\models\UserPasskeyUser;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function headers_sent;
use function is_array;
use function is_string;

/**
 * Verifies the passkey, then creates the user (without password) and logs in
 */
final class PasskeySignupFinishAction extends BaseAction
{
    public function run()
    {
        if (!Yii::$app->user->getIsGuest()) {
            throw new BadRequestHttpException('Already logged in');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $session = Yii::$app->session;
        $pending = $session->get(WebAuthnHelper::SESSION_KEY_SIGNUP);
        $session->remove(WebAuthnHelper::SESSION_KEY_SIGNUP);
        if (
            !is_array($pending) ||
            !is_string($pending['challenge'] ?? null) ||
            !is_string($pending['user_handle'] ?? null) ||
            !is_string($pending['screen_name'] ?? null) ||
            !is_string($pending['name'] ?? null)
        ) {
            return $this->failure('no_challenge');
        }

        $attestation = PasskeyAttestationForm::fromRequest(Yii::$app->request);
        if (!$attestation->validate()) {
            return $this->failure('invalid_params');
        }

        if (!$attestation->verify($pending['challenge'])) {
            return $this->failure((string)$attestation->errorCode);
        }

        // Validate again; the screen name may have been taken in the meantime
        $register = new RegisterForm([
            'scenario' => RegisterForm::SCENARIO_PASSKEY,
            'screen_name' => $pending['screen_name'],
            'name' => $pending['name'],
        ]);
        if (!$register->validate()) {
            return PasskeySignupStartAction::validationFailure($register);
        }

        try {
            $user = Yii::$app->db->transaction(
                function () use ($register, $attestation, $pending): User {
                    $user = $register->toUserModel();
                    if (!$user->save()) {
                        throw new RuntimeException('Failed to save the user');
                    }

                    $passkeyUser = Yii::createObject([
                        'class' => UserPasskeyUser::class,
                        'user_id' => $user->id,
                        'user_handle' => $pending['user_handle'],
                    ]);
                    if (!$passkeyUser->save()) {
                        throw new RuntimeException('Failed to save the passkey user');
                    }

                    if (!$attestation->buildPasskey((int)$user->id)->save()) {
                        throw new RuntimeException('Failed to save the passkey');
                    }

                    return $user;
                },
            );
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
            return $this->failure('save_failed');
        }

        $user->refresh();
        if (!$this->login($user)) {
            return $this->failure('login_failed');
        }

        return [
            'result' => true,
            'redirect' => Url::to(['user/profile']),
        ];
    }

    private function login(User $user): bool
    {
        $appUser = Yii::$app->user;
        $appUser->on(
            \yii\web\User::EVENT_AFTER_LOGIN,
            function () use ($user): void {
                UserLoginHistory::login($user, LoginMethod::METHOD_PASSKEY);
                User::onLogin($user, LoginMethod::METHOD_PASSKEY);
            },
        );

        if (!headers_sent()) {
            Yii::$app->session->regenerateID(true);
        }

        return $appUser->login($user, 0);
    }

    /**
     * Returns a failure with a machine-readable code only; the client shows its own translated message
     *
     * @return array{result: false, error: string}
     */
    private function failure(string $error): array
    {
        return ['result' => false, 'error' => $error];
    }
}
