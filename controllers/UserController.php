<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\user\ClearLoginWithTwitterAction;
use app\actions\user\Download2Action;
use app\actions\user\DownloadAction;
use app\actions\user\DownloadSalmon2Action;
use app\actions\user\EditEmailAction;
use app\actions\user\EditEmailVerifyAction;
use app\actions\user\EditIconAction;
use app\actions\user\EditPasswordAction;
use app\actions\user\EditProfileAction;
use app\actions\user\IconTwitterAction;
use app\actions\user\LanguageAction;
use app\actions\user\LoginAction;
use app\actions\user\LoginHistoryAction;
use app\actions\user\LoginWithTwitterAction;
use app\actions\user\LogoutAction;
use app\actions\user\MachineTranslationAction;
use app\actions\user\ProfileAction;
use app\actions\user\RegenerateApikeyAction;
use app\actions\user\RegisterAction;
use app\actions\user\SlackAddAction;
use app\actions\user\SlackDeleteAction;
use app\actions\user\SlackSuspendAction;
use app\actions\user\SlackTestAction;
use app\actions\user\TimezoneAction;
use app\actions\user\UpdateLoginWithTwitterAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class UserController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'clear-login-with-twitter',
                    'edit-email',
                    'edit-email-verify',
                    'edit-icon',
                    'edit-password',
                    'edit-profile',
                    'icon-twitter',
                    'login',
                    'login-history',
                    'login-with-twitter',
                    'logout',
                    'profile',
                    'regenerate-apikey',
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
                            'edit-email',
                            'edit-email-verify',
                            'edit-icon',
                            'edit-password',
                            'edit-profile',
                            'icon-twitter',
                            'login-history',
                            'logout',
                            'profile',
                            'regenerate-apikey',
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
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => [ 'get' ],
                    'edit-email' => [ 'get', 'post' ],
                    'edit-email-verify' => [ 'post' ],
                    'edit-icon' => [ 'get', 'post' ],
                    'edit-password' => [ 'get', 'post' ],
                    'edit-profile' => [ 'get', 'post' ],
                    'language' => [ 'post' ],
                    'login' => [ 'get', 'post' ],
                    'machine-translation' => [ 'post' ],
                    'regenerate-apikey' => [ 'post' ],
                    'register' => [ 'get', 'post' ],
                    'slack-add' => [ 'get', 'post' ],
                    'slack-delete' => [ 'post' ],
                    'slack-suspend' => [ 'post' ],
                    'slack-test' => [ 'post' ],
                    'timezone' => [ 'post' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'clear-login-with-twitter' => [ 'class' => ClearLoginWithTwitterAction::class ],
            'download' => [ 'class' => DownloadAction::class ],
            'download-salmon' => [ 'class' => DownloadSalmon2Action::class ],
            'download2' => [ 'class' => Download2Action::class ],
            'edit-email' => [ 'class' => EditEmailAction::class ],
            'edit-email-verify' => [ 'class' => EditEmailVerifyAction::class ],
            'edit-icon' => [ 'class' => EditIconAction::class ],
            'edit-password' => [ 'class' => EditPasswordAction::class ],
            'edit-profile' => [ 'class' => EditProfileAction::class ],
            'icon-twitter' => [ 'class' => IconTwitterAction::class ],
            'language' => [ 'class' => LanguageAction::class ],
            'login' => [ 'class' => LoginAction::class ],
            'login-history' => [ 'class' => LoginHistoryAction::class ],
            'login-with-twitter' => [ 'class' => LoginWithTwitterAction::class ],
            'logout' => [ 'class' => LogoutAction::class ],
            'machine-translation' => [ 'class' => MachineTranslationAction::class ],
            'profile' => [ 'class' => ProfileAction::class ],
            'regenerate-apikey' => [ 'class' => RegenerateApikeyAction::class ],
            'register' => [ 'class' => RegisterAction::class ],
            'slack-add' => [ 'class' => SlackAddAction::class ],
            'slack-delete' => [ 'class' => SlackDeleteAction::class ],
            'slack-suspend' => [ 'class' => SlackSuspendAction::class ],
            'slack-test' => [ 'class' => SlackTestAction::class ],
            'timezone' => [ 'class' => TimezoneAction::class ],
            'update-login-with-twitter' => [ 'class' => UpdateLoginWithTwitterAction::class ],
        ];
    }
}
