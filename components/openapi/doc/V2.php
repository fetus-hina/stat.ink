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
            '/api/v2/stage' => $this->getPathInfoStage(),
        ];
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
}
