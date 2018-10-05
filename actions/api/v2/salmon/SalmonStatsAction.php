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
use app\models\SalmonStats2;
use app\models\api\v2\PostSalmonStatsForm;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class SalmonStatsAction extends \yii\web\ViewAction
{
    public function run()
    {
        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';

        if (Yii::$app->user->isGuest) {
            throw new UnauthorizedHttpException('Unauthorized');
        }

        switch (strtoupper(Yii::$app->request->method)) {
            case 'GET':
            case 'HEAD':
                return $this->runGet();

            case 'POST':
                return $this->runPost();

            default:
                throw new MethodNotAllowedHttpException('Method not allowed');
        }
    }

    private function runGet(): array
    {
        $query = SalmonStats2::find()
            ->andWhere(['user_id' => Yii::$app->user->id])
            ->orderBy(['as_of' => SORT_DESC])
            ->limit(1);

        $id = Yii::$app->request->get('id');
        if ($id != '') {
            if (filter_var($id, FILTER_VALIDATE_INT) === false) {
                throw new BadRequestHttpException('Bad Request: id');
            }

            $query->andWhere(['id' => (int)$id]);
        }

        if (!$model = $query->one()) {
            throw new NotFoundHttpException('Not Found');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';
        return $model->toJsonArray();
    }

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
        $resp->headers->set(
            'Location',
            Url::to(['api-v2/salmon-stats', 'id' => $form->created_id], true)
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
            'API/SalmonStat Error: RemoteAddr=[%s], Data=%s',
            $_SERVER['REMOTE_ADDR'],
            Json::encode(
                ['error' => $errors],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        ));
    }
}
