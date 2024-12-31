<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\controllers;

use Yii;
use app\actions\site\IndexAction;
use app\actions\site\LicenseAction;
use app\actions\site\SimpleAction;
use app\actions\site\StartAction;
use app\components\web\AssetPublishAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\web\ErrorAction;

use function defined;
use function implode;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'only' => ['asset-publish'],
                'rules' => [
                    [
                        'ips' => ['127.*', '::1'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
                'view' => 'error',
            ],
            'asset-publish' => [
                'class' => AssetPublishAction::class,
                //FIXME!!!!!!!!!!!!!!!!
                'classes' => [
                    'jp3cki\yii2\datetimepicker\BootstrapDateTimePickerAsset',
                    'jp3cki\yii2\flot\FlotAsset',
                    'jp3cki\yii2\flot\FlotPieAsset',
                    'jp3cki\yii2\flot\FlotResizeAsset',
                    'jp3cki\yii2\flot\FlotStackAsset',
                    'jp3cki\yii2\flot\FlotSymbolAsset',
                    'jp3cki\yii2\flot\FlotTimeAsset',
                    'jp3cki\yii2\zxcvbn\ZxcvbnAsset',
                    'statink\yii2\anonymizer\AnonymizerAsset',
                    'statink\yii2\sortableTable\SortableTableAsset',
                    'statink\yii2\twitter\webintents\TwitterWebIntentsAsset',
                    'yii\bootstrap\BootstrapAsset',
                    'yii\bootstrap\BootstrapPluginAsset',
                    'yii\web\JqueryAsset',
                    'yii\web\YiiAsset',
                ],
            ],
            'index' => [
                'class' => IndexAction::class,
            ],
            'license' => [
                'class' => LicenseAction::class,
            ],
            'privacy' => [
                'class' => SimpleAction::class,
                'view' => 'privacy',
            ],
            'start' => [
                'class' => StartAction::class,
            ],
            'kamiup' => [
                'class' => SimpleAction::class,
                'view' => 'kamiup',
            ],
            'faq' => [
                'class' => SimpleAction::class,
                'view' => 'faq',
            ],
            'color' => [
                'class' => SimpleAction::class,
                'view' => 'color',
            ],
            'translate' => [
                'class' => SimpleAction::class,
                'view' => 'translate',
            ],
        ];
    }

    public function actionApi()
    {
        $this->redirect('https://github.com/fetus-hina/stat.ink/blob/master/API.md');
    }

    public function actionRobots()
    {
        $resp = Yii::$app->response;
        $resp->format = 'raw';
        $resp->headers->set('Content-Type', 'text/plain; charset=UTF-8');
        switch (defined('YII_ENV') ? YII_ENV : '') {
            case 'prod':
                $resp->content = implode("\n", [
                    'User-agent: *',
                    'Disallow:',
                    '',
                    'User-agent: Baiduspider',
                    'Disallow: /',
                ]);
                break;

            default:
                $resp->content = implode("\n", [
                    'User-agent: *',
                    'Disallow: /',
                ]);
        }
    }
}
