<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/IkaLogLog/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\components\web\Controller;

class UserController extends Controller
{
    public $layout = "main.tpl";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [ 'login', 'register' ],
                        'roles' => ['?'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [ 'logout' ],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => [ 'get', 'post' ],
                    'register' => [ 'get', 'post' ],
                    '*' => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\user';
        return [
            'login'     => [ 'class' => $prefix . '\LoginAction' ],
            'logout'    => [ 'class' => $prefix . '\LogoutAction' ],
            'register'  => [ 'class' => $prefix . '\RegisterAction' ],
        ];
    }
}
