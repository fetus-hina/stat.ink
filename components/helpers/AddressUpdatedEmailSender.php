<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use ParagonIE\ConstantTime\Base32;
use Yii;
use app\models\Language;
use app\models\User;

use function random_bytes;
use function strtolower;
use function substr;

final class AddressUpdatedEmailSender
{
    public static function generateVerifyCode(): string
    {
        $random = Base32::encode(random_bytes(5));
        return strtolower(substr($random, 0, 5));
    }

    public static function sendAddressUpdatedEmail(
        ?string $oldEmail,
        ?string $newEmail,
        User $user,
        ?Language $language,
    ): void {
        $languageId = $language?->getLanguageId() ?? Yii::$app->language;

        $mail = Yii::$app->mailer->compose(
            ['text' => '@app/views/email/update-email'],
            [
                'user' => $user,
                'lang' => $languageId,
                'old' => $oldEmail,
                'new' => $newEmail,
            ],
        );
        $mail->setFrom(Yii::$app->params['notifyEmail'])
            ->setSubject(Yii::t(
                'app-email',
                '[{site}] {name} (@{screen_name}): Your email address has been updated',
                [
                    'name' => $user->name,
                    'screen_name' => $user->screen_name,
                    'site' => Yii::$app->name,
                ],
                $languageId,
            ));
        if ($newEmail) {
            $mail->setTo($newEmail);
            if ($oldEmail) {
                $mail->setCc($oldEmail);
            }
        } elseif ($oldEmail) {
            $mail->setTo($oldEmail);
        } else {
            return;
        }
        $mail->send();
    }
}
