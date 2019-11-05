<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\openapi\doc;

use Yii;
use app\models\Map2;
use app\models\Mode2;
use app\models\Weapon2;
use yii\helpers\ArrayHelper;

class V2 extends Base
{
    public function getTitle(): string
    {
        return Yii::t('app-apidoc2', 'stat.ink API for Splatoon 2');
    }

    public function getPaths(): array
    {
        return [
            // general
            '/api/v2/rule' => $this->getPathInfoMode(),
            '/api/v2/stage' => $this->getPathInfoStage(),
            '/api/v2/weapon' => $this->getPathInfoWeapon(),
        ];
    }

    protected function getPathInfoMode(): array
    {
        // {{{
        $this->registerSchema(Mode2::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getMode',
                'summary' => Yii::t('app-apidoc2', 'Get modes'),
                'description' => Yii::t(
                    'app-apidoc2',
                    'Returns an array of mode information'
                ),
                'tags' => [
                    'general',
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => Mode2::oapiRef(),
                                ],
                                'example' => Mode2::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoStage(): array
    {
        // {{{
        $this->registerSchema(Map2::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getStage',
                'summary' => Yii::t('app-apidoc2', 'Get stages'),
                'description' => Yii::t(
                    'app-apidoc2',
                    'Returns an array of stage information'
                ),
                'tags' => [
                    'general',
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => Map2::oapiRef(),
                                ],
                                'example' => Map2::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoWeapon(): array
    {
        // {{{
        $this->registerSchema(Weapon2::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getWeapon',
                'summary' => Yii::t('app-apidoc2', 'Get weapons'),
                'description' => Yii::t(
                    'app-apidoc2',
                    'Returns an array of weapon information'
                ),
                'tags' => [
                    'general',
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => Weapon2::oapiRef(),
                                ],
                                'example' => Weapon2::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }
}
