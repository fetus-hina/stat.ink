<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Exception;
use Throwable;
use Yii;
use app\components\helpers\T;
use app\models\UserIcon;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

final class EditIconAction extends Action
{
    /**
     * @return Response|string
     */
    public function run()
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
                            Yii::t('app', 'Your profile icon has been updated.')
                        );
                        return T::webController($this->controller)
                            ->redirect(['user/profile'], 303);

                    case 'update':
                        $model = DynamicModel::validateData(
                            [
                                'image' => UploadedFile::getInstanceByName('image'),
                            ],
                            [
                                [['image'], 'required', 'message' => 'Please upload a file.'],
                                [['image'], 'image',
                                    'extensions' => ['png', 'jpg'],
                                    'maxSize' => 2 * 1024 * 1024,
                                    'maxWidth' => 2000,
                                    'maxHeight' => 2000,
                                ],
                            ]
                        );
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
                                Yii::t('app', 'Your profile icon has been updated.')
                            );
                            return T::webController($this->controller)
                                ->redirect(['user/profile'], 303);
                        } catch (Throwable $e) {
                            $transaction->rollback();
                            throw $e;
                        }
                }
            } catch (Throwable $e) {
            }
            Yii::$app->session->addFlash(
                'danger',
                $message ?: Yii::t('app', 'Could not update your icon. Please try again.')
            );
        }
        return $this->controller->render('edit-icon', [
            'user' => $user,
            'current' => $user->userIcon,
        ]);
    }
}
