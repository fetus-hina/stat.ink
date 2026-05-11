<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\controllers;

use app\actions\showUser\ProfileAction;
use app\components\web\Controller;
use yii\filters\VerbFilter;

class ShowUserController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'profile' => ['class' => ProfileAction::class],
        ];
    }
}
