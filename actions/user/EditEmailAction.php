<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\AddressUpdatedEmailSender;
use app\models\EmailForm;
use app\models\EmailVerifyForm;
use yii\web\ViewAction;

use function count;
use function time;

class EditEmailAction extends ViewAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $form = Yii::createObject(EmailForm::class);
        if ($request->isPost) {
            $form->load($request->bodyParams);
            if ($form->validate()) {
                $user = Yii::$app->user->identity;
                if (!$form->email) {
                    // Cleared
                    if ($user->email) {
                        $oldEmail = $user->email;
                        $oldEmailLang = $user->emailLang;

                        $user->email = null;
                        $user->email_lang_id = null;
                        $user->save();

                        AddressUpdatedEmailSender::sendAddressUpdatedEmail(
                            $oldEmail ? (string)$oldEmail : null,
                            $form->email ? (string)$form->email : null,
                            $user,
                            $oldEmailLang,
                        );
                    }
                    $this->controller->redirect(['user/profile']);
                    return;
                } elseif ($form->email === $user->email) {
                    $this->controller->redirect(['user/profile']);
                    return;
                }

                // Generate verify code {{{
                $verifyCode = AddressUpdatedEmailSender::generateVerifyCode();
                $session = Yii::$app->session->get('email-verify');
                if (!$session || count($session) >= 50) {
                    $session = [];
                }
                $session[$form->email] = [
                    'code' => $verifyCode,
                    'time' => time(),
                ];
                Yii::$app->session->set('email-verify', $session);
                // }}}

                // Send verify code to user's email address {{{
                $mail = Yii::$app->mailer->compose(
                    ['text' => '@app/views/email/update-email-verify'],
                    [
                        'lang' => Yii::$app->language,
                        'code' => $verifyCode,
                    ],
                );
                $mail->setFrom(Yii::$app->params['notifyEmail'])
                    ->setTo($form->email)
                    ->setSubject(Yii::t(
                        'app-email',
                        '[{site}] {name} (@{screen_name}): Verification code',
                        [
                            'name' => $user->name,
                            'screen_name' => $user->screen_name,
                            'site' => Yii::$app->name,
                        ],
                        Yii::$app->language,
                    ))
                    ->send();
                unset($mail);
                // }}}

                // TODO: Warning email

                $verifyForm = Yii::createObject(EmailVerifyForm::class);
                $verifyForm->realEmail = $form->email;
                return $this->controller->render('edit-email-verify', [
                    'form' => $verifyForm,
                ]);
            }
        }

        return $this->controller->render('edit-email', [
            'form' => $form,
        ]);
    }
}
