<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\TypeHelper;
use app\models\RenameScreenNameForm;
use yii\base\Action;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use function headers_sent;
use function is_array;

final class EditScreenNameAction extends Action
{
    public function run(): string|Response
    {
        $request = Yii::$app->request;
        $form = Yii::createObject(RenameScreenNameForm::class);
        $params = $request->post();
        if (
            $request->isPost &&
            $form->load(is_array($params) ? $params : []) &&
            $form->validate()
        ) {
            if (!$user = Yii::$app->user->identity) {
                throw new ServerErrorHttpException();
            }

            $user->screen_name = TypeHelper::string($form->screen_name);
            $user->save();

            if (!headers_sent()) {
                Yii::$app->session->regenerateID(true);
            }

            return TypeHelper::instanceOf($this->controller, Controller::class)->redirect(['/user/profile']);
        }

        return $this->controller->render('edit-screen-name', [
            'model' => $form,
        ]);
    }
}
