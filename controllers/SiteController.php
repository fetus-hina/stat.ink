<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\actions\site\IndexAction;
use app\actions\site\LicenseAction;
use app\actions\site\SimpleAction;
use app\actions\site\StartAction;
use app\components\web\Controller;
use yii\web\ErrorAction;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
                'view' => 'error',
            ],
            'index' => [
                'class' => IndexAction::class,
            ],
            'license' => [
                'class' => LicenseAction::class,
            ],
            'privacy' => [
                'class' => SimpleAction::class,
                'view' => 'privacy',
            ],
            'start' => [
                'class' => StartAction::class,
            ],
            'kamiup' => [
                'class' => SimpleAction::class,
                'view' => 'kamiup',
            ],
            'faq' => [
                'class' => SimpleAction::class,
                'view' => 'faq',
            ],
            'color' => [
                'class' => SimpleAction::class,
                'view' => 'color',
            ],
            'translate' => [
                'class' => SimpleAction::class,
                'view' => 'translate',
            ],
        ];
    }

    public function actionApi()
    {
        $this->redirect('https://github.com/fetus-hina/stat.ink/blob/master/API.md');
    }

    public function actionRobots()
    {
        $resp = Yii::$app->response;
        $resp->format = 'raw';
        $resp->headers->set('Content-Type', 'text/plain; charset=UTF-8');
        if (YII_ENV_DEV) {
            $resp->content = implode("\n", [
                'User-agent: *',
                'Disallow: /'
            ]);
        } else {
            $resp->content = implode("\n", [
                'User-agent: *',
                'Disallow:',
                '',
                'User-agent: Baiduspider',
                'Disallow: /',
            ]);
        }
    }
}
