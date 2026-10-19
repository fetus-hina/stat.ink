<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

/**
 * A record that links a user to an external OAuth 2.0 account
 */
interface OAuth2LoginLinkInterface
{
    /**
     * Logs in the linked user
     *
     * @return bool
     */
    public function login();
}
