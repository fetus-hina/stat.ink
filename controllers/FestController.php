<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\controllers;

use app\actions\fest\ViewAction;
use app\components\web\Controller;
use yii\filters\VerbFilter;

final class FestController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => [
                        'head',
                        'get',
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'view' => ViewAction::class,
        ];
    }
}
