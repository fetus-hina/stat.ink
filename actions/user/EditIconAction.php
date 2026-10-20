<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Exception;
use Override;
use Throwable;
use Yii;
use app\models\EditIconForm;
use app\models\UserIcon;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;
use yii\web\ViewAction as BaseAction;

use function file_get_contents;

class EditIconAction extends BaseAction
{
    #[Override]
    public function run(): string|Response
    {
        $request = Yii::$app->getRequest();
        if (!$user = Yii::$app->user->identity) {
            throw new ServerErrorHttpException('Internal Server Error');
        }
        if ($request->isPost) {
            $message = null;
            try {
                switch ((string)$request->post('action')) {
                    case 'delete':
                        if ($current = $user->userIcon) {
                            $transaction = Yii::$app->db->beginTransaction();
                            if (!$current->delete()) {
                                throw new Exception();
                            }
                            $transaction->commit();
                        }
                        Yii::$app->session->addFlash(
                            'success',
                            Yii::t('app-user', 'Your profile icon has been updated.'),
                        );
                        return $this->controller->redirect(['user/profile'], 303);

                    case 'update':
                        $model = EditIconForm::validateData([
                            'image' => UploadedFile::getInstanceByName('image'),
                        ]);
                        if ($model->hasErrors()) {
                            $message = $model->getFirstError('image');
                            break;
                        }
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if ($current = $user->userIcon) {
                                if (!$current->delete()) {
                                    throw new Exception();
                                }
                            }
                            if (!$binary = @file_get_contents($model->image->tempName)) {
                                throw new Exception();
                            }
                            $icon = UserIcon::createNew($user->id, $binary);
                            if (!$icon->save()) {
                                throw new Exception();
                            }
                            $transaction->commit();
                            Yii::$app->session->addFlash(
                                'success',
                                Yii::t('app-user', 'Your profile icon has been updated.'),
                            );
                            return $this->controller->redirect(['user/profile'], 303);
                        } catch (Throwable $e) {
                            $transaction->rollback();
                            throw $e;
                        }
                }
            } catch (Throwable $e) {
            }
            Yii::$app->session->addFlash(
                'danger',
                $message ?: Yii::t('app-user', 'Could not update your icon. Please try again.'),
            );
        }
        return $this->controller->render('edit-icon', [
            'user' => $user,
            'current' => $user->userIcon,
        ]);
    }
}
