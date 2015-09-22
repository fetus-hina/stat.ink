<?php
$params = require(__DIR__ . '/params.php');
$config = [
    'name' => 'イカフェスレート',
    'version' => '2.0.0-dev',
    'id' => 'basic',
    'language' => 'ja-jp',
    'timeZone' => 'Asia/Tokyo',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                '<id:\d+>'      => 'fest/view',
                '<id:\d+>.json' => 'fest/view-json',
                'timezone.json' => 'timezone/json',
                'timezone/set'  => 'timezone/set',
                '<action:\w+>'  => 'site/<action>',
                'flash.json'    => 'fest/emulate-official-json',
                'index.json'    => 'fest/index-json',
                ''              => 'fest/index',
            ],
        ],
        'request' => [
            'cookieValidationKey' => include(__DIR__ . '/cookie-secret.php'),
        ],
        'response' => [
            'class' => 'app\components\web\Response',
            'formatters' => [
                'json' => 'app\components\web\PrettyJsonResponseFormatter',
            ],
        ],
        'view' => [
            'renderers' => [
                'tpl' => [
                    'class' => 'yii\smarty\ViewRenderer',
                    'options' => [
                        'force_compile' => defined('YII_DEBUG') && YII_DEBUG,
                        'left_delimiter' => '{{',
                        'right_delimiter' => '}}',
                    ],
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
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
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
