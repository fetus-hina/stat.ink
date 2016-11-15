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
                    'clear-login-with-twitter',
                    'edit-icon',
                    'edit-password',
                    'edit-profile',
                    'icon-twitter',
                    'login',
                    'login-with-twitter',
                    'logout',
                    'profile',
                    'register',
                    'slack-add',
                    'slack-delete',
                    'slack-suspend',
                    'slack-test',
                    'update-login-with-twitter',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'login',
                            'login-with-twitter',
                            'register',
                        ],
                        'roles' => ['?'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'clear-login-with-twitter',
                            'edit-icon',
                            'edit-password',
                            'edit-profile',
                            'icon-twitter',
                            'logout',
                            'profile',
                            'slack-add',
                            'slack-delete',
                            'slack-suspend',
                            'slack-test',
                            'update-login-with-twitter',
                        ],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'edit-icon'     => [ 'get', 'post' ],
                    'edit-password' => [ 'get', 'post' ],
                    'edit-profile'  => [ 'get', 'post' ],
                    'language'      => [ 'post' ],
                    'login'         => [ 'get', 'post' ],
                    'register'      => [ 'get', 'post' ],
                    'slack-add'     => [ 'get', 'post' ],
                    'slack-delete'  => [ 'post' ],
                    'slack-suspend' => [ 'post' ],
                    'slack-test'    => [ 'post' ],
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
            'clear-login-with-twitter' => [ 'class' => $prefix . '\ClearLoginWithTwitterAction' ],
            'download'      => [ 'class' => $prefix . '\DownloadAction' ],
            'edit-icon'     => [ 'class' => $prefix . '\EditIconAction' ],
            'edit-password' => [ 'class' => $prefix . '\EditPasswordAction' ],
            'edit-profile'  => [ 'class' => $prefix . '\EditProfileAction' ],
            'icon-twitter'  => [ 'class' => $prefix . '\IconTwitterAction' ],
            'language'      => [ 'class' => $prefix . '\LanguageAction' ],
            'login'         => [ 'class' => $prefix . '\LoginAction' ],
            'login-with-twitter' => [ 'class' => $prefix . '\LoginWithTwitterAction' ],
            'logout'        => [ 'class' => $prefix . '\LogoutAction' ],
            'profile'       => [ 'class' => $prefix . '\ProfileAction' ],
            'register'      => [ 'class' => $prefix . '\RegisterAction' ],
            'slack-add'     => [ 'class' => $prefix . '\SlackAddAction' ],
            'slack-delete'  => [ 'class' => $prefix . '\SlackDeleteAction' ],
            'slack-suspend' => [ 'class' => $prefix . '\SlackSuspendAction' ],
            'slack-test'    => [ 'class' => $prefix . '\SlackTestAction' ],
            'timezone'      => [ 'class' => $prefix . '\TimezoneAction' ],
            'update-login-with-twitter' => [ 'class' => $prefix . '\UpdateLoginWithTwitterAction' ],
        ];
    }
}
