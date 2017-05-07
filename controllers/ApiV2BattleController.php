<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\filters\auth\RequestBodyAuth;
use app\models\Battle2;
use yii\filters\ContentNegotiator;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApiV2BattleController extends Controller
{
    public $enableCsrfValidation = false;

    public function init()
    {
        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';
        parent::init();
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    HttpBearerAuth::class,
                    ['class' => RequestBodyAuth::class, 'tokenParam' => 'apikey'],
                ],
                'except' => [ 'options' ],
                'optional' => [ 'index', 'view' ],
            ],
        ]);
    }

    protected function verbs()
    {
        return [
            'index'   => ['GET', 'HEAD'],
            'view'    => ['GET', 'HEAD'],
            'create'  => ['POST'],
            'options' => ['OPTIONS'],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\api\v2\battle';
        return [
            'create' => [
                'class' => $prefix . '\CreateAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return "TODO";
    }

    public function actionView($id)
    {
        if (!is_string($id) || !preg_match('/^\d+$/', $id)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        if (!$model = Battle2::findOne(['id' => $id])) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $res = Yii::$app->response;
        $res->format = 'json';
        return $model->toJsonArray();
    }

    public function actionOptions($id = null)
    {
        $res = Yii::$app->response;
        if (Yii::$app->request->method !== 'OPTIONS') {
            $res->statusCode = 405;
            return $res;
        }
        $res->statusCode = 200;
        $header = $res->getHeaders();
        $header->set('Allow', implode(
            ', ',
            $id === null
                ? [ 'GET', 'HEAD', 'POST', 'OPTIONS' ]
                : [ 'GET', 'HEAD', /* 'PUT', 'PATCH', 'DELETE', */ 'OPTIONS']
        ));
        $header->set('Access-Control-Allow-Origin', '*');
        $header->set('Access-Control-Allow-Methods', $header->get('Allow'));
        $header->set('Access-Control-Allow-Headers', 'Content-Type, Authenticate');
        $header->set('Access-Control-Max-Age', '86400');
        return $res;
    }
}
