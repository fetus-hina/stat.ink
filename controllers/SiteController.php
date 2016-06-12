<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => 'error.tpl',
            ],
            'index' => [
                'class' => 'app\actions\site\IndexAction',
            ],
            'license' => [
                'class' => 'app\actions\site\LicenseAction',
            ],
            'privacy' => [
                'class' => 'app\actions\site\SimpleAction',
                'view' => 'privacy.tpl',
            ],
            'start' => [
                'class' => 'app\actions\site\StartAction',
            ],
            'kamiup' => [
                'class' => 'app\actions\site\SimpleAction',
                'view' => 'kamiup.tpl',
            ],
            'users' => [
                'class' => 'app\actions\site\UsersAction',
            ],
            'faq' => [
                'class' => 'app\actions\site\SimpleAction',
                'view' => 'faq.tpl',
            ],
            'color' => [
                'class' => 'app\actions\site\SimpleAction',
                'view' => 'color.tpl',
            ],
            'translate' => [
                'class' => 'app\actions\site\SimpleAction',
                'view' => 'translate.tpl',
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
