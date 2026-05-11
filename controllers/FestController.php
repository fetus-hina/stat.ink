<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\controllers;

use app\actions\fest\ViewAction;
use app\components\web\Controller;
use yii\filters\VerbFilter;

class FestController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    '*' => [ 'head', 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'view' => [ 'class' => ViewAction::class ],
        ];
    }
}
