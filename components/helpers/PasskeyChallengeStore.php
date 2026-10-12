<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;
use app\models\PasskeyChallenge;

use function is_string;

/**
 * Keeps pending WebAuthn challenges for the current session
 *
 * The challenge itself is stored in the database and the session only holds
 * an opaque token, so that take() can delete the row atomically. Two
 * concurrent requests of the same session cannot both take a challenge.
 */
final class PasskeyChallengeStore
{
    private const SESSION_KEY_PREFIX = 'passkey.challenge.';

    /**
     * Stores a new challenge, replacing the pending one of the same purpose
     *
     * @param string $challenge the raw (binary) challenge
     * @param int|null $userId the user the challenge is bound to, if logged in
     * @param array<string, mixed>|null $payload data to restore on take()
     */
    public static function issue(
        string $purpose,
        string $challenge,
        ?int $userId,
        ?array $payload = null,
    ): void {
        $token = self::pullToken($purpose);
        if ($token !== null) {
            PasskeyChallenge::deleteAll(['token' => $token]);
        }
        PasskeyChallenge::deleteExpired();

        $model = PasskeyChallenge::create($purpose, $challenge, $userId, $payload);
        Yii::$app->session->set(self::SESSION_KEY_PREFIX . $purpose, $model->token);
    }

    /**
     * Takes the pending challenge out; it can be taken only once
     *
     * Returns null if there is no pending challenge, it has expired, or it
     * was issued for another user.
     */
    public static function take(string $purpose, ?int $userId): ?PasskeyChallenge
    {
        $token = self::pullToken($purpose);
        return $token !== null
            ? PasskeyChallenge::take($token, $purpose, $userId)
            : null;
    }

    /**
     * Removes the token of the purpose from the session and returns it
     */
    private static function pullToken(string $purpose): ?string
    {
        $session = Yii::$app->session;
        $token = $session->get(self::SESSION_KEY_PREFIX . $purpose);
        $session->remove(self::SESSION_KEY_PREFIX . $purpose);
        return is_string($token) && $token !== '' ? $token : null;
    }
}
