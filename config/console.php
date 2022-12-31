<?php

use app\commands\AssetController;
use app\commands\MigrateController;
use yii\caching\FileCache;
use yii\db\Connection;
use yii\gii\Module as GiiModule;
use yii\gii\generators\model\Generator as GiiModelGenerator;
use yii\helpers\ArrayHelper;
use yii\log\FileTarget as FileLogTarget;
use yii\mutex\PgsqlMutex;

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

Yii::$classMap[ArrayHelper::class] = __DIR__ . '/../components/overwrite/yii/helpers/ArrayHelper.php';

$params = require __DIR__ . '/params.php';
if (file_exists(__DIR__ . '/deepl.php')) {
    $params['deepl'] = require __DIR__ . '/deepl.php';
}

$db = @file_exists(__DIR__ . '/db.php')
    ? require(__DIR__ . '/db.php')
    : [
        'class' => Connection::class,
        'dsn' => 'pgsql:host=localhost;dbname=statink',
    ];

return [
    'name' => 'stat.ink',
    'version' => @file_exists(__DIR__ . '/version.php')
        ? require(__DIR__ . '/version.php')
        : 'UNKNOWN',
    'id' => 'statink-console',
    'timeZone' => 'Asia/Tokyo',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'gii',
        'log',
        'queue',
    ],
    'controllerNamespace' => 'app\commands',
    'controllerMap' => [
        'asset' => AssetController::class,
        'migrate' => MigrateController::class,
    ],
    'modules' => [
        'gii' => [
            'class' => GiiModule::class,
            'generators' => [
                'model' => [
                    'class' => GiiModelGenerator::class,
                    'templates' => [
                        'default' => '@app/views/gii/model',
                    ],
                ],
            ],
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => FileLogTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => array_merge(
            require(__DIR__ . '/web/url-manager.php'),
            [
                'baseUrl' => 'https://stat.ink/',
                'hostInfo' => 'https://stat.ink',
            ],
        ),
        'cache' => require(__DIR__ . '/web/cache.php'),
        'db' => $db,
        'i18n' => require(__DIR__ . '/i18n.php'),
        'imgS3' => require(__DIR__ . '/img-s3.php'),
        'pgMutex' => ['class' => PgsqlMutex::class],
        'queue' => require(__DIR__ . '/queue.php'),
        'schemaCache' => require(__DIR__ . '/web/schema-cache.php'),
    ],
    'params' => $params,
    'aliases' => [
        '@web' => 'https://stat.ink',
        '@imageurl' => 'https://img.stat.ink',
        '@jdenticon' => 'https://jdenticon.stat.ink',
    ],
];
