<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
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
            'api' => [
                'class' => 'app\actions\site\ApiDocAction',
            ],
            'license' => [
                'class' => 'app\actions\site\LicenseAction',
            ],
            'privacy' => [
                'class' => 'app\actions\site\SimpleAction',
                'view' => 'privacy.tpl',
            ],
        ];
    }
}
