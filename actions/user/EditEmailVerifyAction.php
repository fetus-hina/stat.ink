<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\AddressUpdatedEmailSender;
use app\models\EmailVerifyForm;
use app\models\Language;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction;

use function time;

class EditEmailVerifyAction extends ViewAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $form = Yii::createObject(EmailVerifyForm::class);
        $form->load($request->bodyParams);
        if (!$realEmail = $form->realEmail) {
            throw new BadRequestHttpException('Bad request');
        }

        if (!$session = Yii::$app->session->get('email-verify')) {
            throw new BadRequestHttpException('Bad request');
        }

        if (!isset($session[$realEmail])) {
            throw new BadRequestHttpException('Bad request');
        }

        $verifyCodeInfo = $session[$realEmail];
        if (time() - $verifyCodeInfo['time'] > 3600) {
            throw new BadRequestHttpException('Bad request');
        }
        $form->validVerifyCode = $verifyCodeInfo['code'];

        if ($form->validate()) {
            $user = Yii::$app->user->identity;
            $oldEmail = null;
            $oldEmailLang = null;
            if ($user->email) {
                $oldEmail = $user->email;
                $oldEmailLang = $user->emailLang;
            }

            $newEmailLang = Language::findOne(['lang' => Yii::$app->getLocale()]);
            $user->email = $realEmail;
            $user->email_lang_id = $newEmailLang->id;
            $user->save();

            AddressUpdatedEmailSender::sendAddressUpdatedEmail(
                $oldEmail,
                $realEmail,
                $user,
                $oldEmailLang ?? $newEmailLang,
            );
            $this->controller->redirect(['user/profile']);
        }

        return $this->controller->render('edit-email-verify', [
            'form' => $form,
        ]);
    }
}
