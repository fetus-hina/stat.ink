<?php
$params = require(__DIR__ . '/params.php');
$config = [
    'name' => 'stat.ink',
    'version' => '1.4.7',
    'id' => 'statink',
    'language' => 'ja-JP',
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
                'login'         => 'user/login',
                'logout'        => 'user/logout',
                'profile'       => 'user/profile',
                'register'      => 'user/register',
                'u/<screen_name:\w+>/<battle:\d+>' => 'show/battle',
                'u/<screen_name:\w+>/<battle:\d+>/edit' => 'show/edit-battle',
                'u/<screen_name:\w+>/stat/<by:[\w-]+>' => 'show/user-stat-<by>',
                'u/<screen_name:\w+>' => 'show/user',
                'api/v1/<action:[\w-]+>' => 'api-v1/<action>',
                'api/internal/<action:[\w-]+>' => 'api-internal/<action>',
                '<action:[\w-]+>'  => 'site/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
                'robots.txt'    => 'site/robots',
                ''              => 'site/index',
            ],
        ],
        'request' => [
            'cookieValidationKey' => include(__DIR__ . '/cookie-secret.php'),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'application/x-msgpack' => 'app\components\web\MessagePackParser',
            ],
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
                    'pluginDirs' => [
                        '//smarty/',
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
                        'app-death' => 'death.php',
                        'app-map' => 'map.php',
                        'app-rank' => 'rank.php',
                        'app-reltime' => 'reltime.php',
                        'app-rule' => 'rule.php',
                        'app-special' => 'special.php',
                        'app-subweapon' => 'subweapon.php',
                        'app-weapon' => 'weapon.php',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
