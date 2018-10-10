<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\actions\api\v2\salmon;

use Yii;
use app\models\api\v2\salmon\PostForm;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\UnauthorizedHttpException;

class PostSalmonAction extends \yii\web\ViewAction
{
    public function run()
    {
        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';

        $form = Yii::createObject(PostForm::class);
        $form->attributes = Yii::$app->request->post();
        if (!$model = $form->save()) {
            return $this->error($form->getErrors(), 400);
        }

        $resp = Yii::$app->response;
        $resp->statusCode = 201;
        $resp->statusText = 'Created';
        $resp->headers->set(
            'Location',
            Url::to(['api-v2/salmon', 'id' => $model->id], true)
        );
        return '';
    }

    private function error(array $errors, int $code): array
    {
        $this->logError($errors);
        return $this->formatError($errors, $code);
    }

    private function formatError(array $errors, int $code): array
    {
        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        $resp->statusCode = $code;
        return [
            'error' => $errors,
        ];
    }

    private function logError(array $errors): void
    {
        Yii::warning(sprintf(
            'API/Salmon Error: RemoteAddr=[%s], Data=%s',
            $_SERVER['REMOTE_ADDR'],
            Json::encode(
                ['error' => $errors],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        ));
    }
}
