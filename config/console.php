<?php
Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = file_exists(__DIR__ . '/db.php') ? require(__DIR__ . '/db.php') : [ 'class' => 'yii\db\Connection' ];

return [
    'name' => 'stat.ink',
    'version' => require(__DIR__ . '/version.php'),
    'id' => 'statink-console',
    'timeZone' => 'Asia/Tokyo',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'templateFile' => '@app/views/migration/migration.php',
        ],
        'gearman' => [
            'class' => 'shakura\yii2\gearman\GearmanController',
            'gearmanComponent' => 'gearman'
        ],
    ],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'generators' => [
                'model' => [
                    'class' => 'yii\gii\generators\model\Generator',
                    'templates' => [
                        'default' => '@app/views/gii/model',
                    ],
                ],
            ],
        ],
    ],
    'components' => [
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
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => array_merge(
            require(__DIR__ . '/url-manager.php'),
            [
                'baseUrl' => 'https://stat.ink/',
                'hostInfo' => 'https://stat.ink',
            ]
        ),
        'i18n' => require(__DIR__ . '/i18n.php'),
        'gearman' => require(__DIR__ . '/gearman.php'),
        'imgS3' => require(__DIR__ . '/img-s3.php'),
        'pgMutex' => [
            'class' => 'yii\mutex\PgsqlMutex',
        ],
    ],
    'params' => $params,
    'aliases' => [
        '@web' => 'https://stat.ink',
        '@imageurl' => 'https://img.stat.ink',
        '@jdenticon' => 'https://jdenticon.stat.ink',
    ],
];
