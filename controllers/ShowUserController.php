<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\components\web\Controller;

class ShowUserController extends Controller
{
    public $layout = "main.tpl";

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*'             => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\showUser';
        return [
            'profile' => ['class' => $prefix . '\ProfileAction'],
        ];
    }
}
