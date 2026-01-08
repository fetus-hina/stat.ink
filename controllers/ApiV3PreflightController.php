<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\controllers;

use Yii;
use app\components\helpers\TypeHelper;
use yii\rest\Controller;
use yii\web\HeaderCollection;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Request;
use yii\web\Response;

use function implode;
use function sort;

use const SORT_FLAG_CASE;
use const SORT_NATURAL;

final class ApiV3PreflightController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';

        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'delete-options' => ['OPTIONS'],
            'post-options' => ['OPTIONS'],
        ];
    }

    public function actionPostOptions(): Response
    {
        return $this->doOptions(post: true);
    }

    public function actionDeleteOptions(): Response
    {
        return $this->doOptions(post: true, delete: true);
    }

    private function doOptions(bool $post = false, bool $delete = false): Response
    {
        $req = TypeHelper::instanceOf(Yii::$app->request, Request::class);
        $res = TypeHelper::instanceOf(Yii::$app->response, Response::class);

        if ($req->method !== 'OPTIONS') {
            throw new MethodNotAllowedHttpException();
        }

        $allowedMethods = [
            'GET',
            'HEAD',
            'OPTIONS',
        ];

        if ($post) {
            $allowedMethods[] = 'POST';
            $allowedMethods[] = 'PUT';
        }

        if ($delete) {
            $allowedMethods[] = 'DELETE';
        }

        sort($allowedMethods, SORT_NATURAL | SORT_FLAG_CASE);

        $allowedHeaders = [
            'Accept',
            'Authorization',
            'Content-Type',
            'Origin',
            'X-Requested-With',
        ];

        $headers = [
            'Access-Control-Allow-Headers' => implode(', ', $allowedHeaders),
            'Access-Control-Allow-Methods' => implode(', ', $allowedMethods),
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Max-Age' => '86400',
            'Allow' => implode(', ', $allowedMethods),
        ];

        $headerCollection = TypeHelper::instanceOf($res->headers, HeaderCollection::class);
        foreach ($headers as $key => $value) {
            $headerCollection->set($key, $value);
        }

        $res->statusCode = 204;
        return $res;
    }
}
