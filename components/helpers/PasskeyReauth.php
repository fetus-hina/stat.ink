<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;

use function is_array;
use function is_int;

/**
 * Tracks a short-lived "the user has just re-authenticated with a passkey"
 * state in the session, used to protect sensitive account operations.
 */
final class PasskeyReauth
{
    public const SESSION_KEY_CHALLENGE = 'passkey.reauth.challenge';
    public const SESSION_KEY_STATE = 'passkey.reauth.state';

    /**
     * Seconds the re-authentication stays valid
     */
    public const TTL = 300;

    /**
     * @return array{user_id: int, at: int}
     */
    public static function buildState(int $userId, int $now): array
    {
        return [
            'user_id' => $userId,
            'at' => $now,
        ];
    }

    public static function isVerified(mixed $state, int $userId, int $now): bool
    {
        if (
            !is_array($state) ||
            !is_int($state['user_id'] ?? null) ||
            !is_int($state['at'] ?? null)
        ) {
            return false;
        }

        $elapsed = $now - $state['at'];
        return $state['user_id'] === $userId &&
            $elapsed >= 0 &&
            $elapsed <= self::TTL;
    }

    public static function markVerified(int $userId): void
    {
        Yii::$app->session->set(
            self::SESSION_KEY_STATE,
            self::buildState($userId, TypeHelper::int($_SERVER['REQUEST_TIME'])),
        );
    }

    /**
     * Checks the re-authentication state and invalidates it (one-time use)
     */
    public static function consume(int $userId): bool
    {
        $session = Yii::$app->session;
        $state = $session->get(self::SESSION_KEY_STATE);
        $session->remove(self::SESSION_KEY_STATE);

        return self::isVerified($state, $userId, TypeHelper::int($_SERVER['REQUEST_TIME']));
    }
}
