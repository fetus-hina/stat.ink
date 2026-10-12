<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use RuntimeException;
use Yii;
use app\models\PasskeyChallenge;
use yii\db\Expression;

use function implode;
use function is_array;
use function is_string;
use function random_bytes;
use function vsprintf;

/**
 * Keeps pending WebAuthn challenges for the current session
 *
 * The challenge itself is stored in the database and the session only holds
 * an opaque token, so that take() can delete the row atomically. Two
 * concurrent requests of the same session cannot both take a challenge.
 */
final class PasskeyChallengeStore
{
    /**
     * Seconds a challenge stays valid (the browser-side timeout is 60 seconds)
     */
    public const TTL = 300;

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
        self::discard($purpose);
        self::deleteExpired();

        $model = Yii::createObject([
            'class' => PasskeyChallenge::class,
            'token' => WebAuthnHelper::base64UrlEncode(random_bytes(32)),
            'purpose' => $purpose,
            'user_id' => $userId,
            'challenge' => WebAuthnHelper::base64UrlEncode($challenge),
            'payload' => $payload,
            'expires_at' => new Expression(
                'NOW() + CAST(:ttl AS INTEGER) * INTERVAL \'1 second\'',
                [':ttl' => self::TTL],
            ),
        ]);
        if (!$model->save()) {
            throw new RuntimeException('Failed to store the passkey challenge');
        }

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
        $session = Yii::$app->session;
        $token = $session->get(self::SESSION_KEY_PREFIX . $purpose);
        $session->remove(self::SESSION_KEY_PREFIX . $purpose);
        if (!is_string($token) || $token === '') {
            return null;
        }

        $db = Yii::$app->db;
        $row = $db
            ->createCommand(
                vsprintf('DELETE FROM %s WHERE %s RETURNING *', [
                    $db->quoteTableName('{{%passkey_challenge}}'),
                    implode(' AND ', [
                        '[[token]] = :token',
                        '[[purpose]] = :purpose',
                        '[[user_id]] IS NOT DISTINCT FROM CAST(:user_id AS INTEGER)',
                        '[[expires_at]] > NOW()',
                    ]),
                ]),
                [
                    ':token' => $token,
                    ':purpose' => $purpose,
                    ':user_id' => $userId,
                ],
            )
            ->queryOne();
        if (!is_array($row)) {
            return null;
        }

        $model = PasskeyChallenge::instantiate($row);
        PasskeyChallenge::populateRecord($model, $row);
        return $model;
    }

    /**
     * Discards the pending challenge of the purpose, if any
     */
    private static function discard(string $purpose): void
    {
        $session = Yii::$app->session;
        $token = $session->get(self::SESSION_KEY_PREFIX . $purpose);
        $session->remove(self::SESSION_KEY_PREFIX . $purpose);
        if (is_string($token) && $token !== '') {
            PasskeyChallenge::deleteAll(['token' => $token]);
        }
    }

    private static function deleteExpired(): void
    {
        PasskeyChallenge::deleteAll(['<=', 'expires_at', new Expression('NOW()')]);
    }
}
