<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\openapi\doc;

use Yii;
use app\models\Ability;
use app\models\Brand;
use app\models\DeathReason;
use app\models\DeathReasonType;
use app\models\Gear;
use app\models\GearType;
use app\models\Map;
use app\models\Rule;
use app\models\Special;
use app\models\StatWeaponMapTrend;
use app\models\Subweapon;
use app\models\Weapon;
use app\models\WeaponType;
use app\models\api\v1\DeleteBattleForm;
use app\models\openapi\Name;
use yii\helpers\ArrayHelper;

class V1 extends Base
{
    public function getTitle(): string
    {
        return Yii::t('app-apidoc1', 'stat.ink API for Splatoon 1');
    }

    public function getPaths(): array
    {
        return [
            // general
            '/api/v1/gear' => $this->getPathInfoGear(),
            '/api/v1/map' => $this->getPathInfoMap(),
            '/api/v1/rule' => $this->getPathInfoRule(),
            '/api/v1/weapon' => $this->getPathInfoWeapon(),
            // stat.ink spec
            '/api/v1/death-reason' => $this->getPathInfoDeathReason(),
            '/api/v1/weapon-trends' => $this->getPathInfoWeaponTrends(),
            // battle
            '/api/v1/battle' => $this->getPathInfoBattle(),
        ];
    }

    protected function getPathInfoGear(): array
    {
        // {{{
        $this->registerSchema(Gear::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getGear',
                'summary' => Yii::t('app-apidoc1', 'Get gears'),
                'description' => Yii::t(
                    'app-apidoc1',
                    'Returns an array of gear information'
                ),
                'tags' => [
                    'general',
                ],
                'parameters' => [
                    [
                        'name' => 'type',
                        'in' => 'query',
                        'description' => implode("\n", [
                            Yii::t(
                                'app-apidoc1',
                                'Filter by key-string of gear type'
                            ),
                            '',
                            WeaponType::oapiKeyValueTable(
                                Yii::t('app-apidoc1', 'Gear Type'),
                                'app-gear',
                                GearType::find()
                                    ->orderBy(['id' => SORT_ASC])
                                    ->all()
                            ),
                        ]),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                GearType::find()
                                    ->orderBy(['id' => SORT_ASC])
                                    ->asArray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                    [
                        'name' => 'brand',
                        'in' => 'query',
                        'description' => implode("\n", [
                            Yii::t(
                                'app-apidoc1',
                                'Filter by key-string of brand'
                            ),
                            '',
                            WeaponType::oapiKeyValueTable(
                                Yii::t('app-apidoc1', 'Brand'),
                                'app-brand',
                                Brand::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->all()
                            ),
                        ]),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                Brand::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->asArray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                    [
                        'name' => 'ability',
                        'in' => 'query',
                        'description' => implode("\n", [
                            Yii::t(
                                'app-apidoc1',
                                'Filter by key-string of ability'
                            ),
                            '',
                            WeaponType::oapiKeyValueTable(
                                Yii::t('app-apidoc1', 'Ability'),
                                'app-ability',
                                Ability::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->all()
                            ),
                        ]),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                Ability::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->asArray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc1', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => Gear::oapiRef(),
                                ],
                                'example' => Gear::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoMap(): array
    {
        // {{{
        $this->registerSchema(Map::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getMap',
                'summary' => Yii::t('app-apidoc1', 'Get stages'),
                'description' => Yii::t(
                    'app-apidoc1',
                    'Returns an array of stage information'
                ),
                'tags' => [
                    'general',
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc1', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => Map::oapiRef(),
                                ],
                                'example' => Map::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoRule(): array
    {
        // {{{
        $this->registerSchema(Rule::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getRule',
                'summary' => Yii::t('app-apidoc1', 'Get game modes'),
                'description' => Yii::t(
                    'app-apidoc1',
                    'Returns an array of game mode information'
                ),
                'tags' => [
                    'general',
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc1', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => Rule::oapiRef(),
                                ],
                                'example' => Rule::openapiExample(),
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
        $this->registerSchema(Weapon::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getWeapon',
                'summary' => Yii::t('app-apidoc1', 'Get weapons'),
                'description' => Yii::t(
                    'app-apidoc1',
                    'Returns an array of weapon information'
                ),
                'tags' => [
                    'general',
                ],
                'parameters' => [
                    [
                        'name' => 'weapon',
                        'in' => 'query',
                        'description' => implode("\n", [
                            Yii::t(
                                'app-apidoc1',
                                'Filter by key-string of the weapon'
                            ),
                            '',
                            Weapon::oapiKeyValueTable(
                                Yii::t('app-apidoc1', 'Weapon'),
                                'app-weapon',
                                Weapon::find()
                                    ->naturalOrder()
                                    ->all()
                            ),
                        ]),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                Weapon::find()
                                    ->naturalOrder()
                                    ->asarray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                    [
                        'name' => 'type',
                        'in' => 'query',
                        'description' => implode("\n", [
                            Yii::t(
                                'app-apidoc1',
                                'Filter by key-string of weapon type'
                            ),
                            '',
                            WeaponType::oapiKeyValueTable(
                                Yii::t('app-apidoc1', 'Weapon Type'),
                                'app-weapon',
                                WeaponType::find()
                                    ->orderBy(['id' => SORT_ASC])
                                    ->all()
                            ),
                        ]),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                WeaponType::find()
                                    ->orderBy(['id' => SORT_ASC])
                                    ->asArray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                    [
                        'name' => 'sub',
                        'in' => 'query',
                        'description' => implode("\n", [
                            Yii::t(
                                'app-apidoc1',
                                'Filter by key-string of sub weapon'
                            ),
                            '',
                            Subweapon::oapiKeyValueTable(
                                Yii::t('app-apidoc1', 'Sub Weapon'),
                                'app-subweapon',
                                Subweapon::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->all()
                            ),
                        ]),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                Subweapon::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->asArray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                    [
                        'name' => 'special',
                        'in' => 'query',
                        'description' => implode("\n", [
                            Yii::t(
                                'app-apidoc1',
                                'Filter by key-string of special weapon'
                            ),
                            '',
                            Special::oapiKeyValueTable(
                                Yii::t('app-apidoc1', 'Special Weapon'),
                                'app-special',
                                Special::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->all()
                            ),
                        ]),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                Special::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->asArray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc1', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => Weapon::oapiRef(),
                                ],
                                'example' => Weapon::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoDeathReason(): array
    {
        // {{{
        $this->registerSchema(DeathReason::class);
        $this->registerTag('statink');
        return [
            'get' => [
                'operationId' => 'getDeathReason',
                'summary' => Yii::t('app-apidoc1', 'Get death reasons'),
                'description' => Yii::t(
                    'app-apidoc1',
                    'Returns an array of death reason information'
                ),
                'tags' => [
                    'statink',
                ],
                'parameters' => [
                    [
                        'name' => 'type',
                        'in' => 'query',
                        'description' => implode("\n", [
                            Yii::t(
                                'app-apidoc1',
                                'Filter by key-string of death reason category'
                            ),
                            '',
                            WeaponType::oapiKeyValueTable(
                                Yii::t('app-apidoc1', 'Category'),
                                'app-death',
                                DeathReasonType::find()
                                    ->orderBy(['id' => SORT_ASC])
                                    ->all()
                            ),
                        ]),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                DeathReasonType::find()
                                    ->orderBy(['id' => SORT_ASC])
                                    ->asArray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc1', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => DeathReason::oapiRef(),
                                ],
                                'example' => DeathReason::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoWeaponTrends(): array
    {
        // {{{
        $this->registerSchema(StatWeaponMapTrend::class);
        $this->registerTag('statink');
        return [
            'get' => [
                'operationId' => 'getWeaponTrends',
                'summary' => Yii::t('app-apidoc1', 'Get trends of weapon'),
                'description' => Yii::t(
                    'app-apidoc1',
                    'Returns an array of trend information'
                ),
                'tags' => [
                    'statink',
                ],
                'parameters' => [
                    [
                        'name' => 'rule',
                        'in' => 'query',
                        'required' => true,
                        'description' => Rule::oapiKeyValueTable(
                            Yii::t('app-apidoc1', 'Mode'),
                            'app-rule',
                            Rule::find()
                                ->orderBy(['id' => SORT_ASC])
                                ->all()
                        ),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                Rule::find()
                                    ->orderBy(['id' => SORT_ASC])
                                    ->asArray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                    [
                        'name' => 'map',
                        'in' => 'query',
                        'required' => true,
                        'description' => Map::oapiKeyValueTable(
                            Yii::t('app-apidoc1', 'Stage'),
                            'app-map',
                            Map::find()
                                ->orderBy(['key' => SORT_ASC])
                                ->all()
                        ),
                        'schema' => [
                            'type' => 'string',
                            'enum' => ArrayHelper::getColumn(
                                Map::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->asArray()
                                    ->all(),
                                'key'
                            ),
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc1', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => StatWeaponMapTrend::oapiRef(),
                                ],
                                'example' => StatWeaponMapTrend::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoBattle(): array
    {
        // {{{
        return [
            'delete' => $this->getPathInfoBattleDelete(),
        ];
        //}}}
    }

    protected function getPathInfoBattleDelete(): array
    {
        // {{{
        $this->registerSchema(DeleteBattleForm::class);
        $this->registerTag('battle');
        return [
            'operationId' => 'deleteBattle',
            'summary' => Yii::t('app-apidoc1', 'Delete a battle'),
            'description' => Yii::t('app-apidoc1', 'Delete a battle'),
            'tags' => [
                'battle',
            ],
            'requestBody' => [
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => DeleteBattleForm::oapiRef(),
                    ],
                    'application/x-msgpack' => [
                        'schema' => DeleteBattleForm::oapiRef(),
                    ],
                ],
            ],
            'responses' => [
                '200' => [
                    'description' => Yii::t('app-apidoc1', 'Deleted'),
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'oneOf' => [
                                    [
                                        'type' => 'object',
                                        'title' => Yii::t('app-apidoc1', 'Deleted'),
                                        'properties' => [
                                            'deleted' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'id' => [
                                                            'type' => 'integer',
                                                            'format' => 'int64',
                                                            'description' => Yii::t(
                                                                'app-apidoc1',
                                                                'Deleted ID'
                                                            ),
                                                        ],
                                                        'error' => [
                                                            'type' => 'string',
                                                            'nullable' => true,
                                                            'description' => Yii::t(
                                                                'app-apidoc1',
                                                                'Should be null'
                                                            ),
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            'not-deleted' => [
                                                'type' => 'array',
                                                'items' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'id' => [
                                                            'type' => 'integer',
                                                            'format' => 'int64',
                                                            'description' => Yii::t(
                                                                'app-apidoc1',
                                                                'ID that failed to delete'
                                                            ),
                                                        ],
                                                        'error' => [
                                                            'type' => 'string',
                                                            'description' => Yii::t(
                                                                'app-apidoc1',
                                                                'Error identifier'
                                                            ),
                                                            'enum' => [
                                                                'not found',
                                                                'user not match',
                                                                'automated result',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'type' => 'object',
                                        'title' => Yii::t(
                                            'app-apidoc1',
                                            'When test=validate'
                                        ),
                                        'properties' => [
                                            'validate' => [
                                                'type' => 'boolean',
                                                'description' => Yii::t(
                                                    'app-apidoc1',
                                                    'Should be true'
                                                ),
                                                'example' => true,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'example' => [
                                'deleted' => [
                                    [
                                        'id' => 42,
                                        'error' => null,
                                    ],
                                ],
                                'not-deleted' => [
                                    [
                                        'id' => 100,
                                        'error' => 'not found',
                                    ],
                                    [
                                        'id' => 101,
                                        'error' => 'automated result',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }
}
