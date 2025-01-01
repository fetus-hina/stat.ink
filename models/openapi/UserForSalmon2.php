<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\openapi;

use Yii;
use app\models\SalmonStats2;
use app\models\User;

use function array_merge;
use function str_replace;

class UserForSalmon2 extends User
{
    use Util;

    public static function oapiRefName(): string
    {
        return str_replace('UserForSalmon2', 'User', parent::oapiRefName());
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'User information'),
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'description' => Yii::t('app-apidoc2', 'Stat.ink user ID'),
                ],
                'name' => [
                    'type' => 'string',
                    'description' => Yii::t('app-apidoc2', 'User name'),
                ],
                'screen_name' => [
                    'type' => 'string',
                    'pattern' => '[a-zA-Z0-9_]{1,15}',
                    'description' => Yii::t('app-apidoc2', 'User\'s screen name'),
                ],
                'url' => [
                    'type' => 'string',
                    'format' => 'uri',
                    'description' => Yii::t('app-apidoc2', 'Profile page URL'),
                ],
                'salmon_url' => [
                    'type' => 'string',
                    'format' => 'uri',
                    'description' => Yii::t('app-apidoc2', 'Salmon Run results page URL'),
                ],
                'battle_url' => [
                    'type' => 'string',
                    'format' => 'uri',
                    'description' => Yii::t('app-apidoc2', 'Battle results page URL'),
                ],
                'join_at' => array_merge(DateTime::openApiSchema(), [
                    'description' => Yii::t('app-apidoc2', 'User registered at'),
                ]),
                'profile' => [
                    'type' => 'object',
                    'description' => Yii::t('app-apidoc2', 'User profile'),
                    'properties' => [
                        'nnid' => [
                            'type' => 'string',
                            'nullable' => true,
                            'description' => Yii::t('app-apidoc2', 'Nintendo Network ID'),
                        ],
                        'friend_code' => [
                            'type' => 'string',
                            'pattern' => 'SW-[0-9]{4}-[0-9]{4}-[0-9]{4}',
                            'nullable' => true,
                            'description' => Yii::t('app-apidoc2', 'Nintendo Switch Friend Code'),
                        ],
                        'twitter' => [
                            'type' => 'string',
                            'pattern' => '[0-9A-Za-z_]{1,15}',
                            'nullable' => true,
                            'description' => Yii::t('app-apidoc2', 'Twitter screen name'),
                        ],
                        'ikanakama' => [
                            'type' => 'string',
                            'nullable' => true,
                            'description' => Yii::t('app-apidoc2', 'Obsoleted and no longer used'),
                        ],
                        'ikanakama2' => [
                            'type' => 'string',
                            'format' => 'uri',
                            'nullable' => true,
                            'description' => Yii::t(
                                'app-apidoc2',
                                '[Ika-Nakama](https://ikanakama.ink/) profile URL',
                            ),
                        ],
                        'environment' => [
                            'type' => 'string',
                            'nullable' => true,
                            'description' => Yii::t(
                                'app-apidoc2',
                                'IkaLog environment. This probably doesn\'t make sense in ' .
                                'Splatoon 2',
                            ),
                        ],
                    ],
                ],
                'stats' => static::oapiRef(SalmonStats2::class),
            ],
            'example' => static::openapiExample(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            SalmonStats2::class,
        ];
    }

    public static function openapiExample(): array
    {
        return [];
    }
}
