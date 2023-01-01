<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\filters\auth;

use yii\filters\auth\AuthMethod;

use function is_string;

class RequestBodyAuth extends AuthMethod
{
    public $tokenParam = 'access-token';

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
