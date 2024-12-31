<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use app\components\helpers\SalmonExportJson3Helper;
use app\components\helpers\UserExportJson3Helper;
use app\models\SalmonExportJson3;
use app\models\User;
use app\models\UserExportJson3;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

use function sprintf;

final class Download3Action extends Action
{
    public function run(): Response
    {
        $user = Yii::$app->user->getIdentity();
        if (!$user) {
            throw new UnauthorizedHttpException();
        }

        return match (Yii::$app->request->get('type')) {
            'salmon-json' => $this->runSalmonJson($user),
            'user-json' => $this->runUserJson($user),
            default => new BadRequestHttpException(
                Yii::t(
                    'yii',
                    'Invalid data received for parameter "{param}".',
                    ['param' => 'type'],
                ),
            ),
        };
    }

    private function runSalmonJson(User $user): Response
    {
        $isReady = SalmonExportJson3::find()
            ->andWhere(['user_id' => $user->id])
            ->exists();
        if (!$isReady) {
            throw new BadRequestHttpException(
                Yii::t('yii', 'You are not allowed to perform this action.'),
            );
        }

        return Yii::$app->response->sendFile(
            SalmonExportJson3Helper::getPath($user),
            sprintf('statink-salmon-%s.json.gz', $user->screen_name),
            [
                'mimeType' => 'application/octet-stream',
                'inline' => false,
            ],
        );
    }

    private function runUserJson(User $user): Response
    {
        $isReady = UserExportJson3::find()
            ->andWhere(['user_id' => $user->id])
            ->exists();
        if (!$isReady) {
            throw new BadRequestHttpException(
                Yii::t('yii', 'You are not allowed to perform this action.'),
            );
        }

        return Yii::$app->response->sendFile(
            UserExportJson3Helper::getPath($user),
            sprintf('statink-%s.json.gz', $user->screen_name),
            [
                'mimeType' => 'application/octet-stream',
                'inline' => false,
            ],
        );
    }
}
