<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use DOMDocument;
use Yii;
use app\actions\ostatus\FeedAction;
use app\actions\ostatus\StartRemoteFollowAction;
use app\models\OstatusRsa;
use app\models\User;
use app\models\api\internal\PubsubhubbubForm;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class OstatusController extends Controller
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
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'pubsubhubbub' => [ 'post' ],
                    'salmon' => [ 'post' ],
                    'start-remote-follow' => [ 'post' ],
                    '*' => [ 'get', 'head' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'battle-atom' => FeedAction::class,
            'feed' => FeedAction::class,
            'start-remote-follow' => StartRemoteFollowAction::class,
        ];
    }

    public function actionHostMeta()
    {
        $resp = Yii::$app->response;
        $resp->format = 'raw';
        $resp->headers->set('Content-Type', 'text/plain');

        $doc = new DOMDocument('1.0', 'UTF-8');
        $root = $doc->appendChild(
            $doc->createElementNS('http://docs.oasis-open.org/ns/xri/xrd-1.0', 'XRD')
        );

        $elem = $root->appendChild($doc->createElement('Link'));
        $elem->setAttribute('rel', 'lrdd');
        $elem->setAttribute('type', 'application/jrd+json');
        $elem->setAttribute('template', Url::to(['/ostatus/webfinger'], true) . '?resource={uri}');

        return $doc->saveXML();
    }

    public function actionWebfinger($resource)
    {
        if (!preg_match('/^(?:acct:)?@?([a-z0-9_]{1,15})@(.+)$/i', (string)$resource, $match)) {
            throw new BadRequestHttpException('Invalid resource');
        }
        if (strtolower($match[2]) !== strtolower(Yii::$app->request->hostName)) {
            throw new BadRequestHttpException('Invalid hostname');
        }
        if (!$user = User::findOne(['screen_name' => $match[1]])) {
            throw new NotFoundHttpException('Invalid username');
        }
        if (!$rsa = $user->ostatusRsa) {
            $rsa = OstatusRsa::factory($user->id);
            if (!$rsa->save()) {
                throw new ServerErrorHttpException('Could not generate new magicsig');
            }
        }
        $resp = Yii::$app->response;
        $resp->format = 'json';
        $url = Url::to(['/show/user', 'screen_name' => $user->screen_name], true);
        $salmon = Url::to(['/ostatus/salmon', 'screen_name' => $user->screen_name], true);
        return [
            'subject' => vsprintf('acct:%s@%s', [
                $user->screen_name,
                strtolower(Yii::$app->request->hostName),
            ]),
            'aliases' => [
                $url,
            ],
            'links' => [
                [
                    'rel' => 'http://webfinger.net/rel/profile-page',
                    'type' => 'text/html',
                    'href' => $url,
                ],
                [
                    'rel' => 'http://schemas.google.com/g/2010#updates-from',
                    'type' => 'application/atom+xml',
                    'href' => Url::to(['/ostatus/feed', 'screen_name' => $user->screen_name], true),
                ],
                [
                    'rel' => 'magic-public-key',
                    'href' => sprintf(
                        'data:%s,%s',
                        'application/magic-public-key',
                        implode('.', [
                            'RSA',
                            $rsa->modulus,
                            $rsa->exponent,
                        ])
                    ),
                ],
                [
                    'rel' => 'salmon',
                    'href' => $salmon,
                ],
                [
                    'rel' => 'http://salmon-protocol.org/ns/salmon-replies',
                    'href' => $salmon,
                ],
                [
                    'rel' => 'http://salmon-protocol.org/ns/salmon-mention',
                    'href' => $salmon,
                ],
                // [
                //     'rel' => 'http://ostatus.org/schema/1.0/subscribe',
                //     'template' => Url::to(['/ostatus/subscribe'], true) . '?profile={uri}',
                // ],
            ],
        ];
    }

    public function actionPubsubhubbub()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();

        $form = Yii::createObject(PubsubhubbubForm::class);
        $form->callback         = $request->post('hub_callback');
        $form->mode             = $request->post('hub_mode');
        $form->topic            = $request->post('hub_topic');
        $form->lease_seconds    = $request->post('hub_lease_seconds');
        $form->secret           = $request->post('hub_secret');

        if (!$form->validate()) {
            $response = Yii::$app->getResponse();
            $response->statusCode   = 400;
            $response->statusText   = 'Bad Request';
            $response->format       = 'json';
            $response->data = [
                'error' => 'Bad Request',
                'details' => $form->getErrors(),
            ];
        } elseif (!$form->save()) {
            $response->statusCode   = 500;
            $response->statusText   = 'Internal Server Error';
            $response->format       = 'json';
            $response->data = [
                'error' => 'Internal Server Error',
            ];
        } else {
            $response->statusCode   = 202;
            $response->statusText   = 'Accepted';
            $response->format       = 'raw';
            $response->data         = '';
        }
        return $response;
    }

    //TODO
    public function actionSalmon()
    {
        $response = Yii::$app->getResponse();
        $response->statusCode   = 202;
        $response->statusText   = 'Accepted';
        $response->format       = 'raw';
        $response->data         = '';
        return $response;
    }
}
