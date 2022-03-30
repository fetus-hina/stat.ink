<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\filters\auth;

use yii\filters\auth\AuthMethod;
use yii\web\IdentityInterface;

final class RequestBodyAuth extends AuthMethod
{
    public $tokenParam = 'access-token';

    /**
     * @inheritdoc
     * @return IdentityInterface|null
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->post($this->tokenParam);
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, static::class);
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }
        return null;
    }
}
