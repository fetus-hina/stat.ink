<?php

/**
 * @copyright Copyright (C) 2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\components\web\Controller;
use yii\filters\VerbFilter;

class ApiThemeController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'set' => [ 'post' ],
                ],
            ],
        ];
    }

    public function actionSet()
    {
        $resp = Yii::$app->response;
        $resp->format = 'json';

        $theme = Yii::$app->theme;
        $themeId = (string)Yii::$app->request->post('theme');
        if ($theme->isValidTheme($themeId)) {
            Yii::$app->theme->setTheme($themeId);
            return true;
        }

        $resp->statusCode = 400;
        return false;
    }
}
