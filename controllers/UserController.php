<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
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
                'only' => [
                    'edit-password',
                    'edit-profile',
                    'login',
                    'logout',
                    'profile',
                    'register',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'login',
                            'register',
                        ],
                        'roles' => ['?'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'edit-password',
                            'edit-profile',
                            'logout',
                            'profile',
                        ],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'edit-password' => [ 'get', 'post' ],
                    'edit-profile'  => [ 'get', 'post' ],
                    'language'      => [ 'post' ],
                    'login'         => [ 'get', 'post' ],
                    'register'      => [ 'get', 'post' ],
                    'timezone'      => [ 'post' ],
                    '*'             => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\user';
        return [
            'download'      => [ 'class' => $prefix . '\DownloadAction' ],
            'edit-password' => [ 'class' => $prefix . '\EditPasswordAction' ],
            'edit-profile'  => [ 'class' => $prefix . '\EditProfileAction' ],
            'language'      => [ 'class' => $prefix . '\LanguageAction' ],
            'login'         => [ 'class' => $prefix . '\LoginAction' ],
            'logout'        => [ 'class' => $prefix . '\LogoutAction' ],
            'profile'       => [ 'class' => $prefix . '\ProfileAction' ],
            'register'      => [ 'class' => $prefix . '\RegisterAction' ],
            'timezone'      => [ 'class' => $prefix . '\TimezoneAction' ],
        ];
    }
}
