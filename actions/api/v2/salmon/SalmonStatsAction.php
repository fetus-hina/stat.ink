<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\actions\api\v2\salmon;

use DateTimeZone;
use Yii;
use app\models\api\v2\PostSalmonStatsForm;
use yii\helpers\Json;
use yii\web\MethodNotAllowedHttpException;

class SalmonStatsAction extends \yii\web\ViewAction
{
    public function run()
    {
        Yii::$app->language = 'en-US';

        switch (strtoupper(Yii::$app->request->method)) {
            // case 'GET':
            // case 'HEAD':
            //     return $this->runGet();

            case 'POST':
                return $this->runPost();

            default:
                throw new MethodNotAllowedHttpException('Method not allowed');
        }
    }

    // private function runGet()
    // {
    // }

    private function runPost()
    {
        $form = Yii::createObject(PostSalmonStatsForm::class);
        $form->attributes = Yii::$app->request->post();
        if (!$form->save()) {
            return $this->error($form->getErrors(), 400);
        }

        $resp = Yii::$app->response;
        $resp->statusCode = 201;
        $resp->statusText = 'Created';
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
            'API/SalmonStat Error: RemoteAddr=[%s], Data=%s',
            $_SERVER['REMOTE_ADDR'],
            Json::encode(
                ['error' => $errors],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        ));
    }
}
