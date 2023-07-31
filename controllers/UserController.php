<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\user\ClearLoginWithTwitterAction;
use app\actions\user\Download2Action;
use app\actions\user\Download3Action;
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
use app\actions\user\ResetPasswordApikeyAction;
use app\actions\user\SlackAddAction;
use app\actions\user\SlackDeleteAction;
use app\actions\user\SlackSuspendAction;
use app\actions\user\SlackTestAction;
use app\actions\user\TimezoneAction;
use app\actions\user\UpdateLoginWithTwitterAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

final class UserController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'clear-login-with-twitter',
                    'download',
                    'download2',
                    'download3',
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
                    'reset-password-apikey',
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
                            'reset-password-apikey',
                        ],
                        'roles' => ['?'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'clear-login-with-twitter',
                            'download',
                            'download2',
                            'download3',
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
                    'reset-password-apikey' => [ 'get', 'post' ],
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
            'clear-login-with-twitter' => ClearLoginWithTwitterAction::class,
            'download' => DownloadAction::class,
            'download-salmon' => DownloadSalmon2Action::class,
            'download2' => Download2Action::class,
            'download3' => Download3Action::class,
            'edit-email' => EditEmailAction::class,
            'edit-email-verify' => EditEmailVerifyAction::class,
            'edit-icon' => EditIconAction::class,
            'edit-password' => EditPasswordAction::class,
            'edit-profile' => EditProfileAction::class,
            'icon-twitter' => IconTwitterAction::class,
            'language' => LanguageAction::class,
            'login' => LoginAction::class,
            'login-history' => LoginHistoryAction::class,
            'login-with-twitter' => LoginWithTwitterAction::class,
            'logout' => LogoutAction::class,
            'machine-translation' => MachineTranslationAction::class,
            'profile' => ProfileAction::class,
            'regenerate-apikey' => RegenerateApikeyAction::class,
            'register' => RegisterAction::class,
            'reset-password-apikey' => ResetPasswordApikeyAction::class,
            'slack-add' => SlackAddAction::class,
            'slack-delete' => SlackDeleteAction::class,
            'slack-suspend' => SlackSuspendAction::class,
            'slack-test' => SlackTestAction::class,
            'timezone' => TimezoneAction::class,
            'update-login-with-twitter' => UpdateLoginWithTwitterAction::class,
        ];
    }
}
