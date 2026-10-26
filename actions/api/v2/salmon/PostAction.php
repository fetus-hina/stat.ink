<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\v2\salmon;

use Override;
use Yii;
use app\models\api\v2\salmon\PostForm;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\ViewAction;

use function sprintf;
use function is_array;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class PostAction extends ViewAction
{
    #[Override]
    public function init(): void
    {
        parent::init();

        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';
    }

    /**
     * @return array{error: array<string, string[]>}|string
     */
    #[Override]
    public function run(): array|string
    {
        $form = Yii::createObject(PostForm::class);
        $params = Yii::$app->request->post();
        $form->attributes = is_array($params) ? $params : [];
        if (!$model = $form->save()) {
            return $this->error($form->getErrors(), 400);
        }

        $resp = Yii::$app->response;
        if ($form->is_found) {
            $resp->statusCode = 302;
            $resp->statusText = 'Found';
        } else {
            $resp->statusCode = 201;
            $resp->statusText = 'Created';
        }

        $headers = $resp->headers;
        $headers->set(
            'location',
            Url::to(
                ['salmon/view',
                    'screen_name' => $model->user->screen_name,
                    'id' => $model->id,
                ],
                true,
            ),
        );
        $headers->set(
            'x-api-location',
            Url::to(['api-v2-salmon/view', 'id' => $model->id], true),
        );

        return '';
    }

    /**
     * @param array<string, string[]> $errors
     * @return array{error: array<string, string[]>}
     */
    private function error(array $errors, int $code): array
    {
        $this->logError($errors);
        return $this->formatError($errors, $code);
    }

    /**
     * @param array<string, string[]> $errors
     * @return array{error: array<string, string[]>}
     */
    private function formatError(array $errors, int $code): array
    {
        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        $resp->statusCode = $code;
        return [
            'error' => $errors,
        ];
    }

    /**
     * @param array<string, string[]> $errors
     */
    private function logError(array $errors): void
    {
        Yii::warning(sprintf(
            'API/Salmon Error: RemoteAddr=[%s], Data=%s',
            $_SERVER['REMOTE_ADDR'],
            Json::encode(
                ['error' => $errors],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            ),
        ));
    }
}
