<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use Zend\Feed\Writer\Feed as FeedWriter;
use app\models\OstatusRsa;
use app\models\User;
use jp3cki\uuid\NS as UuidNs;
use jp3cki\uuid\Uuid;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
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

    public function actionHostMeta()
    {
        $resp = Yii::$app->response;
        $resp->format = 'json';
        return [
            'links' => [
                [
                    'rel' => 'lrdd',
                    'type' => 'application/json',
                    'template' => Url::to(['/ostatus/webfinger'], true) . '?resource={uri}',
                ],
                [
                    'rel' => 'lrdd',
                    'type' => 'application/jrd+json',
                    'template' => Url::to(['/ostatus/webfinger'], true) . '?resource={uri}',
                ],
            ],
        ];
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
            'subject' => sprintf('acct:%s@%s', $user->screen_name, strtolower(Yii::$app->request->hostName)),
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
                [
                    'rel' => 'http://ostatus.org/schema/1.0/subscribe',
                    'template' => Url::to(['/ostatus/subscribe'], true) . '?profile={uri}',
                ],
            ],
        ];
    }

    public function actionFeed(string $screen_name)
    {
        Yii::$app->language = 'ja-JP';
        Yii::$app->timeZone = 'Etc/UTC';
        $resp = Yii::$app->getResponse();

        if (!$user = User::findOne(['screen_name' => $screen_name])) {
            $resp->format = 'json';
            $resp->statusCode = 404;
            $resp->statusText = 'File Not Found';
            return ['error' => Yii::t('yii', 'Page not found.')];
        }

        $resp->format = 'raw';
        $resp->headers->set('Content-Type', 'application/atom+xml; charset=UTF-8');

        return $this->render('atom.tpl', [
            'user' => $user,
        ]);
    }
}
