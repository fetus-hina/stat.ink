<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use Closure;
use Throwable;
use Yii;
use app\models\LoginMethod;
use app\models\User;

/**
 * Sends account notification emails to the user's registered address
 *
 * A notification must not break the operation that triggered it, so failures
 * are logged and reported only by the return value.
 */
final class UserNotifier
{
    public static function loggedIn(User $user, int $loginMethod): bool
    {
        return self::send(
            $user,
            '@app/views/email/login',
            fn (array $params, string $language): string => Yii::t(
                'app-email',
                '[{site}] {name} (@{screen_name}): Logged in',
                $params,
                $language,
            ),
            fn (): array => ['method' => LoginMethod::findOne(['id' => $loginMethod])],
        );
    }

    public static function passwordChanged(User $user): bool
    {
        return self::send(
            $user,
            '@app/views/email/change-password',
            fn (array $params, string $language): string => Yii::t(
                'app-email',
                '[{site}] {name} (@{screen_name}): Changed your password',
                $params,
                $language,
            ),
        );
    }

    public static function passwordDisabled(User $user): bool
    {
        return self::send(
            $user,
            '@app/views/email/disable-password',
            fn (array $params, string $language): string => Yii::t(
                'app-email',
                '[{site}] {name} (@{screen_name}): Disabled your password',
                $params,
                $language,
            ),
        );
    }

    public static function passkeyRegistered(User $user, string $nickname): bool
    {
        return self::send(
            $user,
            '@app/views/email/register-passkey',
            fn (array $params, string $language): string => Yii::t(
                'app-email',
                '[{site}] {name} (@{screen_name}): Passkey registered',
                $params,
                $language,
            ),
            fn (): array => ['nickname' => $nickname],
        );
    }

    public static function passkeyDeleted(User $user, string $nickname): bool
    {
        return self::send(
            $user,
            '@app/views/email/delete-passkey',
            fn (array $params, string $language): string => Yii::t(
                'app-email',
                '[{site}] {name} (@{screen_name}): Passkey deleted',
                $params,
                $language,
            ),
            fn (): array => ['nickname' => $nickname],
        );
    }

    /**
     * The subject and the view parameters are built inside the try block, as
     * they may also fail (e.g., loading the email language)
     *
     * @param Closure(array{name: string, screen_name: string, site: string}, string): string $subject
     *   builds the subject from the parameters and the language
     * @param (Closure(): array<string, mixed>)|null $params builds additional view parameters
     */
    private static function send(User $user, string $view, Closure $subject, ?Closure $params = null): bool
    {
        if (!$user->email) {
            return false;
        }

        try {
            $sent = Yii::$app->mailer
                ->compose(['text' => $view], ['user' => $user, ...($params ? $params() : [])])
                ->setFrom(Yii::$app->params['notifyEmail'])
                ->setTo([$user->email => $user->name])
                ->setSubject($subject(self::subjectParams($user), self::language($user)))
                ->send();
            if (!$sent) {
                Yii::warning("Failed to send a notification email ({$view})", __METHOD__);
            }
            return $sent;
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
            return false;
        }
    }

    /**
     * @return array{name: string, screen_name: string, site: string}
     */
    private static function subjectParams(User $user): array
    {
        return [
            'name' => (string)$user->name,
            'screen_name' => (string)$user->screen_name,
            'site' => (string)Yii::$app->name,
        ];
    }

    private static function language(User $user): string
    {
        return $user->emailLang->lang ?? 'en-US';
    }
}
