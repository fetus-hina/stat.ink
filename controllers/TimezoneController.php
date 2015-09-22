<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;
use yii\filters\VerbFilter;

class TimezoneController extends Controller
{
    public $layout = "main.tpl";

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'set' => [ 'post' ],
                    '*' => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\timezone';
        return [
            'json' => [ 'class' => $prefix . '\JsonAction' ],
            'set' => [ 'class' => $prefix . '\SetAction' ],
        ];
    }
}
