<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\controllers;

use Yii;
use app\actions\site\IndexAction;
use app\actions\site\LicenseAction;
use app\actions\site\SimpleAction;
use app\actions\site\StartAction;
use app\components\web\AssetPublishAction;
use app\components\web\Controller;
use jp3cki\yii2\datetimepicker\BootstrapDateTimePickerAsset;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotPieAsset;
use jp3cki\yii2\flot\FlotResizeAsset;
use jp3cki\yii2\flot\FlotStackAsset;
use jp3cki\yii2\flot\FlotSymbolAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use jp3cki\yii2\zxcvbn\ZxcvbnAsset;
use statink\yii2\anonymizer\AnonymizerAsset;
use statink\yii2\sortableTable\SortableTableAsset;
use statink\yii2\twitter\webintents\TwitterWebIntentsAsset;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\filters\AccessControl;
use yii\web\ErrorAction;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

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
                'classes' => [
                    AnonymizerAsset::class,
                    BootstrapAsset::class,
                    BootstrapDateTimePickerAsset::class,
                    BootstrapPluginAsset::class,
                    FlotAsset::class,
                    FlotPieAsset::class,
                    FlotResizeAsset::class,
                    FlotStackAsset::class,
                    FlotSymbolAsset::class,
                    FlotTimeAsset::class,
                    JqueryAsset::class,
                    SortableTableAsset::class,
                    TwitterWebIntentsAsset::class,
                    YiiAsset::class,
                    ZxcvbnAsset::class,
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
                $disallow = [
                    'Disallow: /api/internal/',
                    'Disallow: /login',
                    'Disallow: /logout',
                    'Disallow: /register',
                    'Disallow: /user/',
                    'Disallow: /*?filter',
                    'Disallow: /*?f%5B',
                ];
                $resp->content = implode("\n", [
                    'User-agent: *',
                    ...$disallow,
                    '',
                    'User-agent: Googlebot',
                    'User-agent: Bingbot',
                    'User-agent: DuckDuckBot',
                    'User-agent: YandexBot',
                    'User-agent: Slurp',
                    'User-agent: Applebot',
                    ...$disallow,
                    'Crawl-delay: 20',
                    '',
                    'User-agent: GPTBot',
                    'User-agent: ChatGPT-User',
                    'User-agent: OAI-SearchBot',
                    'User-agent: ClaudeBot',
                    'User-agent: Claude-User',
                    'User-agent: Claude-SearchBot',
                    'User-agent: anthropic-ai',
                    'User-agent: Google-Extended',
                    'User-agent: Applebot-Extended',
                    'User-agent: Meta-ExternalAgent',
                    'User-agent: Meta-ExternalFetcher',
                    'User-agent: PerplexityBot',
                    'User-agent: Perplexity-User',
                    'User-agent: Bytespider',
                    'User-agent: CCBot',
                    'User-agent: Amazonbot',
                    'User-agent: cohere-ai',
                    'User-agent: DuckAssistBot',
                    ...$disallow,
                    'Crawl-delay: 60',
                    '',
                    'User-agent: AhrefsBot',
                    'User-agent: SemrushBot',
                    'User-agent: MJ12bot',
                    'User-agent: DotBot',
                    'User-agent: PetalBot',
                    'Disallow: /',
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
