<?php
use app\models\LoginMethod;
use app\models\UserLoginHistory;
use yii\web\UserEvent;

$params = require(__DIR__ . '/params.php');
$authKeySecret = @file_exists(__DIR__ . '/authkey-secret.php')
    ? include(__DIR__ . '/authkey-secret.php')
    : null;
$config = [
    'name' => 'stat.ink',
    'version' => @file_exists(__DIR__ . '/version.php')
        ? require(__DIR__ . '/version.php')
        : 'UNKNOWN',
    'id' => 'statink',
    'language' => 'ja-JP',
    'timeZone' => 'Asia/Tokyo',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'autoAlias'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'class' => 'app\components\web\AssetManager',
            'appendTimestamp' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [ 'jquery.min.js' ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [ 'css/bootstrap.min.css' ],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [ 'js/bootstrap.min.js' ],
                ],
            ],
            'converter' => [
                'class' => 'app\components\web\AssetConverter',
            ],
        ],
        'urlManager' => require(__DIR__ . '/url-manager.php'),
        'request' => [
            'class' => 'app\components\web\Request',
            'cookieValidationKey' => include(__DIR__ . '/cookie-secret.php'),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'application/x-msgpack' => 'app\components\web\MessagePackParser',
            ],
        ],
        'response' => [
            'class' => 'app\components\web\Response',
            'formatters' => [
                'csv'           => 'app\components\web\CsvResponseFormatter',
                'ikalog-json'   => 'app\components\web\IkalogJsonResponseFormatter',
                'json'          => 'app\components\web\PrettyJsonResponseFormatter',
                'compact-json'  => 'yii\web\JsonResponseFormatter',
            ],
        ],
        'view' => [
            'renderers' => [
                'tpl' => [
                    'class' => 'yii\smarty\ViewRenderer',
                    'options' => [
                        'force_compile' => false, //defined('YII_DEBUG') && YII_DEBUG,
                        'left_delimiter' => '{{',
                        'right_delimiter' => '}}',
                    ],
                    'pluginDirs' => [
                        '//smarty/',
                    ],
                    'widgets' => [
                        'functions' => [
                            'AdWidget' => 'app\components\widgets\AdWidget',
                            'BattleFilterWidget' => 'app\components\widgets\BattleFilterWidget',
                            'Battle2FilterWidget' => 'app\components\widgets\Battle2FilterWidget',
                            'SnsWidget' => 'app\components\widgets\SnsWidget',
                            'WinLoseLegend' => 'app\components\widgets\WinLoseLegend',
                        ]
                    ],
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache-v2',
        ],
        'schemaCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/schema-cache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'user' => [
            'class' => 'app\components\web\User',
            'identityFixedKey' => $authKeySecret,
            'identityClass' => 'app\models\User',
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'secure' => !!preg_match('/(?:^|\.)stat\.ink$/i', $_SERVER['HTTP_HOST'] ?? ''),
            ],
            'enableAutoLogin' => $authKeySecret !== null,
            'loginUrl' => ['user/login'],
            'on afterLogin' => function (UserEvent $event): void {
                if (!$event->cookieBased) {
                    return;
                }
                UserLoginHistory::login(
                    Yii::$app->user->identity,
                    LoginMethod::METHOD_COOKIE
                );
            },
        ],
        'i18n' => require(__DIR__ . '/i18n.php'),
        'formatter' => [
            'class' => 'app\components\i18n\Formatter',
        ],
        'mutex' => [
            'class' => 'yii\mutex\FileMutex',
            'dirMode' => 0700,
            'fileMode' => 0600,
        ],
        'pgMutex' => [
            'class' => 'yii\mutex\PgsqlMutex',
        ],
        'session' => [
            'timeout' => 86400,
            'cookieParams' => [
                'httponly' => true,
                'secure' => !!preg_match('/(?:^|\.)stat\.ink$/i', $_SERVER['HTTP_HOST'] ?? ''),
            ],
        ],
        'autoAlias' => [
            'class' => 'app\components\AutoAlias',
            'aliases' => [
                '@imageurl' => function () use ($params) : string {
                    return ($params['useImgStatInk'] ?? false)
                        ? 'https://img.stat.ink'
                        : '@web/images';
                },
                '@jdenticon' => 'https://jdenticon.stat.ink',
            ],
        ],
        'gearman' => require(__DIR__ . '/gearman.php'),
        'imgS3' => require(__DIR__ . '/img-s3.php'),
        'weaponShortener' => [
            'class' => 'app\components\helpers\WeaponShortener',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => require(__DIR__ . '/debug-ips.php'),
    ];
}
unset($authKeySecret);

return $config;
