<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Base32\Base32;
use Yii;
use app\models\Language;
use app\models\User;

class AddressUpdatedEmailSender
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
        Language $language
    ): void {
        $mail = Yii::$app->mailer->compose(
            ['text' => '@app/views/email/update-email'],
            [
                'user' => $user,
                'lang' => $language->getLanguageId(),
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
                $language->getLanguageId(),
            ));
        if ($newEmail) {
            $mail->setTo($newEmail);
            if ($oldEmail) {
                $mail->setCc($oldEmail);
            }
        } else {
            $mail->setTo($oldEmail);
        }
        $mail->send();
    }
}
