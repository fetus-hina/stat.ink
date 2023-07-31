<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use app\components\helpers\TypeHelper;
use app\models\ResetPasswordApikeyForm;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

use function compact;
use function implode;
use function is_string;

final class ResetPasswordApikeyAction extends Action
{
    public function run(): string|Response
    {
        $cfToken = ArrayHelper::getValue(Yii::$app->params, 'cloudflareTurnstile.siteKey');
        if (!is_string($cfToken)) {
            throw new InvalidConfigException('Cloudflare Turnstile is not configured.');
        }

        $request = TypeHelper::instanceOf(Yii::$app->request, Request::class);
        $form = Yii::createObject(ResetPasswordApikeyForm::class);
        if ($request->isPost) {
            if ($form->load($request->post())) {
                $form->cf_turnstile_response = (string)$request->post('cf-turnstile-response');
                if (
                    $form->validate() &&
                    $form->updatePassword()
                ) {
                    $form->sendEmail();

                    Yii::$app->session->addFlash(
                        'success',
                        implode("\n", [
                            Yii::t('app', 'Your password has been changed successfully.'),
                            Yii::t('app', 'Please log in with your new password.'),
                        ]),
                    );

                    return TypeHelper::instanceOf($this->controller, Controller::class)
                      ->redirect(['user/login']);
                }
            }
        }

        // Clear sensitive data
        $form->password = null;
        $form->password_repeat = null;
        $form->cf_turnstile_response = null;

        return $this->controller->render('reset-password-apikey', compact('cfToken', 'form'));
    }
}
