<?php
$params = require(__DIR__ . '/params.php');
$config = [
    'name' => 'stat.ink',
    'version' => require(__DIR__ . '/version.php'),
    'id' => 'statink',
    'language' => 'ja-JP',
    'timeZone' => 'Asia/Tokyo',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'autoAlias'],
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
                'app\assets\D3Asset' => [
                    'js' => [ 'https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js' ],
                ],
                'app\assets\CalHeatmapAsset' => [
                    'js' => [ 'cal-heatmap.min.js' ],
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
                            'IpVersionBadgeWidget' => 'app\components\widgets\IpVersionBadgeWidget',
                            'JdenticonWidget' => 'app\components\widgets\JdenticonWidget',
                            'SnsWidget' => 'app\components\widgets\SnsWidget',
                            'WinLoseLegend' => 'app\components\widgets\WinLoseLegend',
                        ]
                    ],
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'serializer' => extension_loaded('msgpack')
                ? [
                    function ($value) {
                        return @gzencode(msgpack_pack($value), 1, FORCE_GZIP);
                    },
                    function ($value) {
                        return @msgpack_unpack(gzdecode($value));
                    },
                ]
                : null,
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
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'loginUrl' => ['user/login'],
        ],
        'i18n' => require(__DIR__ . '/i18n.php'),
        'mutex' => [
            'class' => 'yii\mutex\FileMutex',
            'dirMode' => 0700,
            'fileMode' => 0600,
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
                '@imageurl' => function () {
                    return (\Yii::$app->request->hostInfo === 'https://stat.ink')
                        ? 'https://img.stat.ink'
                        : '@web/images';
                },
                '@jdenticon' => 'https://jdenticon.stat.ink',
            ],
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

return $config;
