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
                'login'         => 'user/login',
                'logout'        => 'user/logout',
                'profile'       => 'user/profile',
                'register'      => 'user/register',
                'u/<screen_name:\w+>/<battle:\d+>' => 'show/battle',
                'u/<screen_name:\w+>/<battle:\d+>/edit' => 'show/edit-battle',
                'u/<screen_name:\w+>/stat/report/<year:\d+>/<month:\d+>' => 'show/user-stat-report',
                'u/<screen_name:\w+>/stat/report/<year:\d+>' => 'show/user-stat-report',
                'u/<screen_name:\w+>/stat/<by:[\w-]+>' => 'show/user-stat-<by>',
                'u/<screen_name:\w+>' => 'show/user',
                'u/<screen_name:\w+>.<lang:[\w-]+>.<type:rss|atom>' => 'feed/user',
                'fest/<region:\w+>/<order:\d+>' => 'fest/view',
                'entire/weapons/<weapon:\w+>/<rule:\w+>' => 'entire/weapon',
                'entire/weapons/<weapon:\w+>' => 'entire/weapon',
                'entire/users/<b32name:[A-Za-z2-7]+>' => 'entire/agent',
                'api/v1/<action:[\w-]+>' => 'api-v1/<action>',
                'api/internal/<action:[\w-]+>' => 'api-internal/<action>',
                '<action:[\w-]+>'  => 'site/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
                'robots.txt'    => 'site/robots',
                ''              => 'site/index',
            ],
        ],
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
                'csv' => 'app\components\web\CsvResponseFormatter',
                'ikalog-json' => 'app\components\web\IkalogJsonResponseFormatter',
                'json' => 'app\components\web\PrettyJsonResponseFormatter',
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
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app-ability'   => 'ability.php',
                        'app-brand'     => 'brand.php',
                        'app-death'     => 'death.php',
                        'app-fest'      => 'fest.php',
                        'app-gear'      => 'gear.php',
                        'app-gearstat'  => 'gearstat.php',
                        'app-map'       => 'map.php',
                        'app-rank'      => 'rank.php',
                        'app-reltime'   => 'reltime.php',
                        'app-rule'      => 'rule.php',
                        'app-special'   => 'special.php',
                        'app-start'     => 'start.php',
                        'app-subweapon' => 'subweapon.php',
                        'app-tz'        => 'tz.php',
                        'app-weapon'    => 'weapon.php',
                    ],
                ],
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
