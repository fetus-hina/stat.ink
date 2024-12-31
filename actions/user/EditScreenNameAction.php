<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\models\RenameScreenNameForm;
use yii\base\Action;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use function headers_sent;

final class EditScreenNameAction extends Action
{
    public function run(): string|Response
    {
        $request = Yii::$app->request;
        $form = Yii::createObject(RenameScreenNameForm::class);
        if (
            $request->isPost &&
            $form->load($request->post()) &&
            $form->validate()
        ) {
            if (!$user = Yii::$app->user->identity) {
                throw new ServerErrorHttpException();
            }

            $user->screen_name = $form->screen_name;
            $user->save();

            if (!headers_sent()) {
                Yii::$app->session->regenerateID(true);
            }

            return $this->controller->redirect(['/user/profile']);
        }

        return $this->controller->render('edit-screen-name', [
            'model' => $form,
        ]);
    }
}
