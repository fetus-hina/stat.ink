<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\openapi\doc;

use Yii;
use app\models\Ability2;
use app\models\Brand2;
use app\models\Gear2;
use app\models\GearType;
use app\models\Language;
use app\models\Map2;
use app\models\Mode2;
use app\models\Salmon2;
use app\models\SalmonMap2;
use app\models\SalmonStats2;
use app\models\Special2;
use app\models\Subweapon2;
use app\models\UserStat2;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use app\models\WeaponType2;
use app\models\api\v2\GearGetForm;
use app\models\api\v2\PostSalmonStatsForm;
use app\models\api\v2\salmon\PostForm as PostSalmonForm;
use app\models\openapi\SplatNet2ID;
use app\models\openapi\Util as OpenApiUtil;
use app\models\openapi\sec\ApiToken;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\UnsetArrayValue;
use yii\helpers\Url;

class V2 extends Base
{
    use OpenApiUtil;

    public function getTitle(): string
    {
        return Yii::t('app-apidoc2', 'stat.ink API for Splatoon 2');
    }

    public function getPaths(): array
    {
        return [
            // general
            '/api/v2/gear' => $this->getPathInfoGear(),
            '/api/v2/gear.csv' => $this->getPathInfoGearCsv(),
            '/api/v2/rule' => $this->getPathInfoMode(),
            '/api/v2/stage' => $this->getPathInfoStage(),
            '/api/v2/weapon' => $this->getPathInfoWeapon(),
            '/api/v2/weapon.csv' => $this->getPathInfoWeaponCsv(),

            // battle
            '/api/v2/user-stats' => $this->getPathInfoUserStats(),

            // salmon
            '/api/v2/salmon' => $this->getPathInfoSalmon(),
            '/api/v2/salmon/{id}' => $this->getPathInfoSalmonWithID(),
            '/api/v2/user-salmon' => $this->getPathInfoUserSalmon(),
            '/api/v2/salmon-stats' => $this->getPathInfoSalmonStats(),

            // obsoleted
            '/api/v2/map' => $this->getPathInfoMap(),
        ];
    }

    protected function getPathInfoGear(): array
    {
        // {{{
        $this->registerSchema(Gear2::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getGear',
                'summary' => Yii::t('app-apidoc2', 'Get gears'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns an array of gear information'
                    )),
                    vsprintf('%s: %s', [
                        Html::encode(Yii::t('app-apidoc2', 'HTML version')),
                        implode(' / ', [
                            Html::a(
                                Html::encode(Yii::t('app-gear', 'Headgear')),
                                'https://stat.ink/api-info/gear2-headgear'
                            ),
                            Html::a(
                                Html::encode(Yii::t('app-gear', 'Clothing')),
                                'https://stat.ink/api-info/gear2-clothing'
                            ),
                            Html::a(
                                Html::encode(Yii::t('app-gear', 'Shoes')),
                                'https://stat.ink/api-info/gear2-shoes'
                            ),
                        ]),
                    ]),
                ]),
                'tags' => [
                    'general',
                ],
                'parameters' => GearGetForm::oapiParameters(),
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => Gear2::oapiRef(),
                                ],
                                'example' => Gear2::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoGearCsv(): array
    {
        // {{{
        $this->registerSchema(SplatNet2ID::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getGearCsv',
                'summary' => Yii::t('app-apidoc2', 'Get gears in CSV format'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns all of gear information in CSV (RFC 4180) format.'
                    )),
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'As the number of supported languages changes, the position of items ' .
                        'may change.'
                    )),
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Be sure to check the header on the first line (or use JSON version) if ' .
                        'you use this data for automatic processing.'
                    )),
                    vsprintf('%s: %s', [
                        Html::encode(Yii::t('app-apidoc2', 'HTML version')),
                        implode(' / ', [
                            Html::a(
                                Html::encode(Yii::t('app-gear', 'Headgear')),
                                'https://stat.ink/api-info/gear2-headgear'
                            ),
                            Html::a(
                                Html::encode(Yii::t('app-gear', 'Clothing')),
                                'https://stat.ink/api-info/gear2-clothing'
                            ),
                            Html::a(
                                Html::encode(Yii::t('app-gear', 'Shoes')),
                                'https://stat.ink/api-info/gear2-shoes'
                            ),
                        ]),
                    ]),
                ]),
                'tags' => [
                    'general',
                ],
                'parameters' => GearGetForm::oapiParameters(),
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'text/csv' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'description' => Yii::t(
                                            'app-apidoc2',
                                            'Gear information'
                                        ),
                                        'properties' => array_merge([
                                            'type' => static::oapiKey(
                                                implode("\n", [
                                                    Yii::t('app-apidoc2', 'Gear category'),
                                                    '',
                                                    static::oapiKeyValueTable(
                                                        Yii::t('app-apidoc2', 'Gear category'),
                                                        'app-gear',
                                                        GearType::find()
                                                            ->orderBy(['id' => SORT_ASC])
                                                            ->all()
                                                    ),
                                                ]),
                                                ArrayHelper::getColumn(
                                                    GearType::find()
                                                        ->orderBy(['id' => SORT_ASC])
                                                        ->all(),
                                                    'key',
                                                    false
                                                ),
                                                true
                                            ),
                                            'brand' => static::oapiKey(
                                                implode("\n", [
                                                    Yii::t('app-apidoc2', 'Brand'),
                                                    '',
                                                    static::oapiKeyValueTable(
                                                        Yii::t('app-apidoc2', 'Brand'),
                                                        'app-brand2',
                                                        Brand2::find()
                                                            ->orderBy(['key' => SORT_ASC])
                                                            ->all()
                                                    ),
                                                ]),
                                                ArrayHelper::getColumn(
                                                    Brand2::find()
                                                        ->orderBy(['key' => SORT_ASC])
                                                        ->all(),
                                                    'key',
                                                    false
                                                ),
                                                true
                                            ),
                                            'splatnet' => static::oapiRef(SplatNet2ID::class),
                                            'primary_ability' => array_merge(
                                                static::oapiKey(
                                                    implode("\n", [
                                                        Yii::t('app-apidoc2', 'Primary ability'),
                                                        '',
                                                        static::oapiKeyValueTable(
                                                            Yii::t(
                                                                'app-apidoc2',
                                                                'Primary ability'
                                                            ),
                                                            'app-ability2',
                                                            Ability2::find()
                                                                ->orderBy(['key' => SORT_ASC])
                                                                ->all()
                                                        ),
                                                    ]),
                                                    ArrayHelper::getColumn(
                                                        Ability2::find()
                                                            ->orderBy(['key' => SORT_ASC])
                                                            ->all(),
                                                        'key',
                                                        false
                                                    ),
                                                    true
                                                ),
                                                ['nullable' => true]
                                            ),
                                        ], (function (): array {
                                            $langs = Language::find()
                                                ->standard()
                                                ->orderBy(['lang' => SORT_ASC])
                                                ->all();
                                            $ret = [];
                                            foreach ($langs as $lang) {
                                                $ret['[' . $lang->lang . ']'] = [
                                                    'type' => 'string',
                                                    'description' => $lang->name,
                                                ];
                                            }
                                            return $ret;
                                        })()),
                                    ],
                                ],
                                'example' => implode("\n", array_map(
                                    function (Gear2 $gear): string {
                                        static $langs = null;
                                        if ($langs === null) {
                                            $langs = Language::find()
                                                ->standard()
                                                ->orderBy(['lang' => SORT_ASC])
                                                ->all();
                                        }
                                        $row = [
                                            (string)$gear->type->key,
                                            (string)$gear->brand->key,
                                            (string)$gear->key,
                                            (string)$gear->splatnet,
                                            (string)($gear->ability->key ?? ''),
                                        ];
                                        foreach ($langs as $lang) {
                                            $row[] = (string)Yii::$app->i18n->translate(
                                                'app-gear2',
                                                $gear->name,
                                                [],
                                                $lang->lang
                                            );
                                        }
                                        return implode(',', array_map(
                                            function (string $cell): string {
                                                if (!preg_match('/["\x0d\x0a,]/', $cell)) {
                                                    return $cell;
                                                }

                                                return '"' . str_replace('"', '""', $cell) . '"';
                                            },
                                            $row
                                        ));
                                    },
                                    Gear2::find()
                                        ->innerJoinWith([
                                            'type',
                                            'brand',
                                        ])
                                        ->with([
                                            'ability',
                                            'brand.strength',
                                            'brand.weakness',
                                        ])
                                        ->orderBy([
                                            '{{gear2}}.[[type_id]]' => SORT_ASC,
                                            '{{gear2}}.[[key]]' => SORT_ASC,
                                        ])
                                        ->limit(5)
                                        ->all()
                                )),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
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
                'description' => implode("\n\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns an array of stage information'
                    )),
                    Html::a(
                        Html::encode(Yii::t('app-apidoc2', 'HTML version')),
                        'https://stat.ink/api-info/stage2'
                    ),
                ]),
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
                'description' => implode("\n\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns an array of weapon information'
                    )),
                    Html::a(
                        Html::encode(Yii::t('app-apidoc2', 'HTML version')),
                        'https://stat.ink/api-info/weapon2'
                    ),
                ]),
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

    protected function getPathInfoWeaponCsv(): array
    {
        // {{{
        $this->registerSchema(SplatNet2ID::class);
        $this->registerTag('general');
        return [
            'get' => [
                'operationId' => 'getWeaponCsv',
                'summary' => Yii::t('app-apidoc2', 'Get weapons in CSV format'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns all of weapon information in CSV (RFC 4180) format.'
                    )),
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'As the number of supported languages changes, the position of items ' .
                        'may change.'
                    )),
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Be sure to check the header on the first line (or use JSON version) if ' .
                        'you use this data for automatic processing.'
                    )),
                    Html::a(
                        Html::encode(Yii::t('app-apidoc2', 'HTML version')),
                        'https://stat.ink/api-info/weapon2'
                    ),
                ]),
                'tags' => [
                    'general',
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'text/csv' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'description' => Yii::t(
                                            'app-apidoc2',
                                            'Weapon information'
                                        ),
                                        'properties' => array_merge([
                                            'category1' => static::oapiKey(
                                                implode("\n", [
                                                    Yii::t('app-apidoc2', 'Weapon category'),
                                                    '',
                                                    Html::tag('table', implode('', [
                                                        Html::tag('thead', Html::tag(
                                                            'tr',
                                                            implode('', [
                                                                '<th><code>category1</code></th>',
                                                                '<th><code>category2</code></th>',
                                                                Html::tag('th', Html::encode(Yii::t(
                                                                    'app-apidoc2',
                                                                    'Weapon category'
                                                                ))),
                                                            ]),
                                                        )),
                                                        Html::tag('tbody', implode('', array_map(
                                                            function (WeaponType2 $type): string {
                                                                return Html::tag('tr', implode('', [
                                                                    sprintf(
                                                                        '<td><code>%s</code></td>',
                                                                        Html::encode(
                                                                            $type->category->key
                                                                        )
                                                                    ),
                                                                    sprintf(
                                                                        '<td><code>%s</code></td>',
                                                                        Html::encode(
                                                                            $type->key
                                                                        )
                                                                    ),
                                                                    Html::tag('td', Html::encode(
                                                                        Yii::t(
                                                                            'app-weapon2',
                                                                            $type->name
                                                                        )
                                                                    )),
                                                                ]));
                                                            },
                                                            WeaponType2::find()
                                                                ->with(['category'])
                                                                ->orderBy([
                                                                    'category_id' => SORT_ASC,
                                                                    'rank' => SORT_ASC,
                                                                ])
                                                                ->all()
                                                        ))),
                                                    ])),
                                                ]),
                                                ArrayHelper::getColumn(
                                                    WeaponCategory2::find()
                                                        ->orderBy(['id' => SORT_ASC])
                                                        ->all(),
                                                    'key',
                                                    false
                                                ),
                                                true
                                            ),
                                            'category2' => static::oapiKey(
                                                Yii::t('app-apidoc2', 'Refer "category1"'),
                                                ArrayHelper::getColumn(
                                                    WeaponType2::find()
                                                        ->orderBy([
                                                            'category_id' => SORT_ASC,
                                                            'rank' => SORT_ASC,
                                                        ])
                                                        ->all(),
                                                    'key',
                                                    false
                                                ),
                                                true
                                            ),
                                            'key' => static::oapiKey(null, array_map(
                                                function (Weapon2 $weapon): string {
                                                    return $weapon->key;
                                                },
                                                Weapon2::find()->orderBy(['key' => SORT_ASC])->all()
                                            )),
                                            'subweapon' => static::oapiKey(
                                                implode("\n", [
                                                    Yii::t('app-apidoc2', 'Sub weapon'),
                                                    '',
                                                    static::oapiKeyValueTable(
                                                        Yii::t('app-apidoc2', 'Sub weapon'),
                                                        'app-subweapon2',
                                                        Subweapon2::find()
                                                            ->orderBy(['key' => SORT_ASC])
                                                            ->all()
                                                    ),
                                                ]),
                                                array_map(
                                                    function (Subweapon2 $sub): string {
                                                        return $sub->key;
                                                    },
                                                    Subweapon2::find()
                                                        ->orderBy(['key' => SORT_ASC])
                                                        ->all()
                                                ),
                                                true // replace description
                                            ),
                                            'special' => static::oapiKey(
                                                implode("\n", [
                                                    Yii::t('app-apidoc2', 'Special weapon'),
                                                    '',
                                                    static::oapiKeyValueTable(
                                                        Yii::t('app-apidoc2', 'Special weapon'),
                                                        'app-special2',
                                                        Special2::find()
                                                            ->orderBy(['key' => SORT_ASC])
                                                            ->all()
                                                    ),
                                                ]),
                                                array_map(
                                                    function (Special2 $sp): string {
                                                        return $sp->key;
                                                    },
                                                    Special2::find()
                                                        ->orderBy(['key' => SORT_ASC])
                                                        ->all()
                                                ),
                                                true // replace description
                                            ),
                                            'mainweapon' => static::oapiKey(
                                                Yii::t(
                                                    'app-apidoc2',
                                                    'This points to the main weapon.'
                                                ),
                                                array_map(
                                                    function (Weapon2 $w): string {
                                                        return $w->key;
                                                    },
                                                    Weapon2::find()
                                                        ->orderBy(['key' => SORT_ASC])
                                                        ->andWhere('[[id]] = [[main_group_id]]')
                                                        ->all()
                                                ),
                                                true // replace description
                                            ),
                                            'reskin' => static::oapiKey(
                                                Yii::t(
                                                    'app-apidoc2',
                                                    'If it is a weapon that only looks ' .
                                                    'different, like the Hero series, this ' .
                                                    'points to the original weapon.'
                                                ),
                                                array_map(
                                                    function (Weapon2 $w): string {
                                                        return $w->key;
                                                    },
                                                    Weapon2::find()
                                                        ->orderBy(['key' => SORT_ASC])
                                                        ->all()
                                                ),
                                                true // replace description
                                            ),
                                            'splatnet' => static::oapiRef(SplatNet2ID::class),
                                        ], (function (): array {
                                            $langs = Language::find()
                                                ->standard()
                                                ->orderBy(['lang' => SORT_ASC])
                                                ->all();
                                            $ret = [];
                                            foreach ($langs as $lang) {
                                                $ret['[' . $lang->lang . ']'] = [
                                                    'type' => 'string',
                                                    'description' => $lang->name,
                                                ];
                                            }
                                            return $ret;
                                        })()),
                                    ],
                                ],
                                'example' => implode("\n", array_map(
                                    function (Weapon2 $weapon): string {
                                        static $langs = null;
                                        if ($langs === null) {
                                            $langs = Language::find()
                                                ->standard()
                                                ->orderBy(['lang' => SORT_ASC])
                                                ->all();
                                        }
                                        $row = [
                                            (string)$weapon->type->category->key,
                                            (string)$weapon->type->key,
                                            (string)$weapon->key,
                                            (string)$weapon->subweapon->key,
                                            (string)$weapon->special->key,
                                            (string)$weapon->mainReference->key,
                                            (string)$weapon->canonical->key,
                                            (string)$weapon->splatnet,
                                        ];
                                        foreach ($langs as $lang) {
                                            $row[] = (string)Yii::$app->i18n->translate(
                                                'app-weapon2',
                                                $weapon->name,
                                                [],
                                                $lang->lang
                                            );
                                        }
                                        return implode(',', array_map(
                                            function (string $cell): string {
                                                if (!preg_match('/["\x0d\x0a,]/', $cell)) {
                                                    return $cell;
                                                }

                                                return '"' . str_replace('"', '""', $cell) . '"';
                                            },
                                            $row
                                        ));
                                    },
                                    Weapon2::find()
                                        ->with([
                                            'canonical',
                                            'mainReference',
                                            'special',
                                            'subweapon',
                                            'type',
                                            'type.category',
                                        ])
                                        ->andWhere(['key' => [
                                            'heroshooter_replica',
                                            'octoshooter_replica',
                                            'sshooter',
                                            'sshooter_becchu',
                                            'sshooter_collabo',
                                        ]])
                                        ->orderBy(['splatnet' => SORT_ASC])
                                        ->all()
                                )),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoUserStats(): array
    {
        // {{{
        $this->registerSchema(UserStat2::class);
        $this->registerTag('battle');
        return [
            'get' => [
                'operationId' => 'getUserStats',
                'summary' => Yii::t('app-apidoc2', 'Get user\'s battle stats'),
                'description' => implode("\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns specified user\'s stats (e.g., how many kills)'
                    )),
                ]),
                'tags' => [
                    'battle',
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => UserStat2::oapiRef(),
                                'example' => UserStat2::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoSalmon(): array
    {
        return array_merge(
            $this->getPathInfoSalmonGet(),
            $this->getPathInfoSalmonPost(),
        );
    }

    protected function getPathInfoSalmonGet(): array
    {
        // {{{
        $this->registerSchema(Salmon2::class);
        $this->registerTag('salmon');
        return [
            'get' => [
                'operationId' => 'getSalmon',
                'summary' => Yii::t('app-apidoc2', 'Get Salmon Run results'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns Salmon Run results.'
                    )),
                ]),
                'tags' => [
                    'salmon',
                ],
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'screen_name',
                        'required' => false,
                        'schema' => [
                            'type' => 'string',
                            'pattern' => '[0-9a-zA-Z_]{1,15}',
                        ],
                        'description' => implode("\n", [
                            Html::encode(Yii::t('app-apidoc2', 'Filter by user')),
                            '',
                            Html::encode(Yii::t(
                                'app-apidoc2',
                                'This parameter is required if you set `only` = `splatnet_number`.'
                            )),
                        ]),
                    ],
                    [
                        'in' => 'query',
                        'name' => 'only',
                        'required' => false,
                        'schema' => [
                            'type' => 'string',
                            'enum' => [
                                'splatnet_number',
                            ],
                        ],
                        'description' => implode("\n", [
                            Html::encode(Yii::t('app-apidoc2', 'Change the result set')),
                            '',
                            static::oapiKeyValueTable(
                                '',
                                'app-apidoc2',
                                [
                                    [
                                        'k' => 'splatnet_number',
                                        'v' => 'Returns only SplatNet\'s ID Numbers',
                                    ],
                                ],
                                'k',
                                'v',
                                Html::encode(Yii::t('app-apidoc2', 'Value'))
                            ),
                        ]),
                    ],
                    [
                        'in' => 'query',
                        'name' => 'stage',
                        'required' => false,
                        'schema' => static::oapiKey(
                            '',
                            ArrayHelper::getColumn(
                                SalmonMap2::find()->orderBy(['key' => SORT_ASC])->asArray()->all(),
                                'key',
                                false
                            ),
                            true
                        ),
                        'description' => implode("\n", [
                            Html::encode(Yii::t('app-apidoc2', 'Filter by stage')),
                            '',
                            static::oapiKeyValueTable(
                                Yii::t('app-apidoc2', 'Stage'),
                                'app-salmon-map2',
                                SalmonMap2::find()
                                    ->orderBy(['key' => SORT_ASC])
                                    ->all()
                            ),
                        ]),
                    ],
                    [
                        'in' => 'query',
                        'name' => 'newer_than',
                        'required' => false,
                        'schema' => [
                            'type' => 'integer',
                            'format' => 'int32',
                        ],
                        'description' => implode("\n", [
                            Html::encode(Yii::t('app-apidoc2', 'Filter by permanent ID')),
                            '',
                            Yii::t(
                                'app-apidoc2',
                                'You\'ll get `newer_than` &lt; `id` &lt; `older_than`.'
                            ),
                        ]),
                    ],
                    [
                        'in' => 'query',
                        'name' => 'older_than',
                        'required' => false,
                        'schema' => [
                            'type' => 'integer',
                            'format' => 'int32',
                        ],
                        'description' => implode("\n", [
                            Html::encode(Yii::t('app-apidoc2', 'Filter by permanent ID')),
                            '',
                            Yii::t(
                                'app-apidoc2',
                                'You\'ll get `newer_than` &lt; `id` &lt; `older_than`.'
                            ),
                        ]),
                    ],
                    [
                        'in' => 'query',
                        'name' => 'order',
                        'schema' => [
                            'type' => 'string',
                            'enum' => [
                                'asc',
                                'desc',
                                'splatnet_asc',
                                'splatnet_desc',
                            ],
                            'default' => 'desc',
                        ],
                        'description' => implode("\n", [
                            Html::encode(Yii::t('app-apidoc2', 'Result order')),
                            '',
                            static::oapiKeyValueTable(
                                '',
                                'app-apidoc2',
                                [
                                    [
                                        'k' => 'asc',
                                        'v' => 'Older to newer',
                                    ],
                                    [
                                        'k' => 'desc',
                                        'v' => 'Newer to older (default in most case)',
                                    ],
                                    [
                                        'k' => 'splatnet_asc',
                                        'v' => 'SplatNet number small to big',
                                    ],
                                    [
                                        'k' => 'splatnet_desc',
                                        'v' => 'SplatNet number big to small (default if ' .
                                            '"only" = "splatnet_number")',
                                    ],
                                ],
                                'k',
                                'v'
                            ),
                        ]),
                    ],
                    [
                        'in' => 'query',
                        'name' => 'count',
                        'required' => false,
                        'schema' => [
                            'type' => 'integer',
                            'format' => 'int32',
                            'minimum' => 1,
                            'maximum' => 1000,
                            'default' => 50,
                        ],
                        'description' => implode("\n", [
                            Html::encode(Yii::t('app-apidoc2', 'Max records to get')),
                            '',
                            implode("<br>\n", [
                                Html::encode(Yii::t(
                                    'app-apidoc2',
                                    'Accepts `1`-`1000` (if `only` = `splatnet_number`)'
                                )),
                                Html::encode(Yii::t(
                                    'app-apidoc2',
                                    'Accepts `1`-`50` (otherwise)'
                                )),
                            ]),
                        ]),
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'oneOf' => [
                                            array_merge(Salmon2::openApiSchema(), [
                                                'title' => Yii::t('app-apidoc2', 'Otherwise'),
                                            ]),
                                            [
                                                'title' => 'only = splatnet_number',
                                                'type' => 'integer',
                                                'format' => 'int32',
                                                'minimum' => 0,
                                                'description' => Yii::t(
                                                    'app-apidoc2',
                                                    'Shift number in SplatNet 2'
                                                ),
                                                'example' => 42,
                                            ],
                                        ],
                                    ],
                                    'example' => [
                                        Salmon2::openapiExample(),
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

    private function getPathInfoSalmonPost(): array
    {
        // {{{
        $this->registerSecurityScheme(ApiToken::class);
        $this->registerTag('salmon');
        $this->registerSchema(PostSalmonForm::class);
        return [
            'post' => [
                'operationId' => 'postSalmon',
                'summary' => Yii::t('app-apidoc2', 'Post Salmon Run results'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t('app-apidoc2', 'Post Salmon Run results')),
                ]),
                'tags' => [
                    'salmon',
                ],
                'security' => [
                    ApiToken::oapiSecUse(),
                ],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'appliation/json' => [
                            'schema' => static::oapiRef(PostSalmonForm::class),
                        ],
                        'application/x-msgpack' => [
                            'schema' => static::oapiRef(PostSalmonForm::class),
                        ],
                    ],
                ],
                'responses' => [
                    '201' => [
                        'description' => Yii::t('app-apidoc2', 'Created'),
                        'headers' => [
                            'Location' => [
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                                'description' => Yii::t('app-apidoc2', 'Public URL'),
                                'example' => Url::to(
                                    ['salmon/view',
                                        'screen_name' => 'fetus_hina',
                                        'id' => 137857,
                                    ],
                                    true
                                ),
                            ],
                            'X-API-Location' => [
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                                'description' => Yii::t(
                                    'app-apidoc2',
                                    'URL for API call that created'
                                ),
                                'example' => Url::to(
                                    ['api-v2-salmon/view', 'id' => 137857],
                                    true
                                ),
                            ],
                        ],
                    ],
                    '302' => [
                        'description' => Yii::t('app-apidoc2', 'Found same data'),
                        'headers' => [
                            'Location' => [
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                                'description' => Yii::t('app-apidoc2', 'Public URL'),
                                'example' => Url::to(
                                    ['salmon/view',
                                        'screen_name' => 'fetus_hina',
                                        'id' => 137857,
                                    ],
                                    true
                                ),
                            ],
                            'X-API-Location' => [
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                                'description' => Yii::t(
                                    'app-apidoc2',
                                    'URL for API call that created'
                                ),
                                'example' => Url::to(
                                    ['api-v2-salmon/view', 'id' => 137857],
                                    true
                                ),
                            ],
                        ],
                    ],
                    '400' => [
                        'description' => 'Bad Request',
                    ],
                    '401' => [
                        'description' => 'Unauthorized',
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoSalmonWithID(): array
    {
        // {{{
        $this->registerSchema(Salmon2::class);
        $this->registerTag('salmon');
        return [
            'get' => [
                'operationId' => 'getSalmonWithID',
                'summary' => Yii::t('app-apidoc2', 'Get the Salmon Run results'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns the Salmon Run results.'
                    )),
                ]),
                'tags' => [
                    'salmon',
                ],
                'parameters' => [
                    [
                        'in' => 'path',
                        'name' => 'id',
                        'required' => true,
                        'schema' => [
                            'type' => 'integer',
                            'format' => 'int32',
                        ],
                        'description' => Yii::t('app-apidoc2', 'Permanent ID of the results'),
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => static::oapiRef(Salmon2::class),
                                'example' => Salmon2::openapiExample(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoUserSalmon(): array
    {
        // {{{
        $data = ArrayHelper::merge(static::getPathInfoSalmon(), [
            'get' => [
                'operationId' => 'getUserSalmon',
                'summary' => Yii::t('app-apidoc2', 'Get Salmon Run results (with Auth)'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns Salmon Run results.'
                    )),
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'You can only get data for user who is authenticated by API token.'
                    )),
                ]),
                'security' => [
                    ApiToken::oapiSecUse(),
                ],
                'responses' => [
                    '401' => [
                        'description' => Yii::t('app-apidoc2', 'Unauthorized'),
                    ],
                ],
            ],
            'post' => new UnsetArrayValue(),
        ]);

        // remove "screen_name" parameter
        $data['get']['parameters'] = array_values(array_filter(
            $data['get']['parameters'],
            function (array $param): bool {
                return $param['name'] !== 'screen_name';
            }
        ));

        return $data;
        // }}}
    }

    protected function getPathInfoSalmonStats(): array
    {
        return array_merge(
            $this->getPathInfoSalmonStatsGet(),
            $this->getPathInfoSalmonStatsPost(),
        );
    }

    private function getPathInfoSalmonStatsGet(): array
    {
        // {{{
        $this->registerSchema(SalmonStats2::class);
        $this->registerSecurityScheme(ApiToken::class);
        $this->registerTag('salmon');
        return [
            'get' => [
                'operationId' => 'getSalmonStats',
                'summary' => Yii::t('app-apidoc2', 'Get Salmon Run stats (card data)'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'Returns Salmon Run stats.'
                    )),
                    Html::encode(Yii::t(
                        'app-apidoc2',
                        'You can only get data for user who is authenticated by API token.'
                    )),
                ]),
                'tags' => [
                    'salmon',
                ],
                'security' => [
                    ApiToken::oapiSecUse(),
                ],
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'id',
                        'required' => false,
                        'schema' => [
                            'type' => 'integer',
                            'format' => 'int32',
                            'minimum' => 1,
                        ],
                        'description' => implode("\n\n", [
                            Html::encode(Yii::t('app-apidoc2', 'Permanent ID')),
                            Yii::t(
                                'app-apidoc2',
                                'If you omitted the <code>id</code>, you will get a latest data.'
                            ),
                            Yii::t(
                                'app-apidoc2',
                                'The value of <code>id</code> is obtained in the Location header ' .
                                'of the POST API.'
                            ),
                            Yii::t(
                                'app-apidoc2',
                                'If you specified other player\'s <code>id</code> value, you ' .
                                'will get the 404 error.'
                            )
                        ]),
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => Yii::t('app-apidoc2', 'Successful'),
                        'content' => [
                            'application/json' => [
                                'schema' => SalmonStats2::oapiRef(),
                                'example' => SalmonStats2::openapiExample(),
                            ],
                        ],
                    ],
                    '400' => [
                        'description' => 'Bad Request',
                    ],
                    '401' => [
                        'description' => 'Unauthorized',
                    ],
                    '404' => [
                        'description' => 'Not Found',
                    ],
                ],
            ],
        ];
        // }}}
    }

    private function getPathInfoSalmonStatsPost(): array
    {
        // {{{
        $this->registerSecurityScheme(ApiToken::class);
        $this->registerTag('salmon');
        $this->registerSchema(PostSalmonStatsForm::class);
        return [
            'post' => [
                'operationId' => 'postSalmonStats',
                'summary' => Yii::t('app-apidoc2', 'Post Salmon Run stats (card data)'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t('app-apidoc2', 'Post Salmon Run stats (card data)')),
                ]),
                'tags' => [
                    'salmon',
                ],
                'security' => [
                    ApiToken::oapiSecUse(),
                ],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'appliation/json' => [
                            'schema' => static::oapiRef(PostSalmonStatsForm::class),
                        ],
                        'application/x-msgpack' => [
                            'schema' => static::oapiRef(PostSalmonStatsForm::class),
                        ],
                    ],
                ],
                'responses' => [
                    '201' => [
                        'description' => Yii::t('app-apidoc2', 'Created'),
                        'headers' => [
                            'Location' => [
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                                'description' => Yii::t(
                                    'app-apidoc2',
                                    'URL for API call that created'
                                ),
                                'example' => Url::to(
                                    ['api-v2-salmon/view-stats', 'id' => 42],
                                    true
                                ),
                            ],
                        ],
                    ],
                    '302' => [
                        'description' => Yii::t('app-apidoc2', 'Found same data'),
                        'headers' => [
                            'Location' => [
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                                'description' => Yii::t('app-apidoc2', 'URL for API call'),
                                'example' => Url::to(
                                    ['api-v2-salmon/view-stats', 'id' => 42],
                                    true
                                ),
                            ],
                        ],
                    ],
                    '400' => [
                        'description' => 'Bad Request',
                    ],
                    '401' => [
                        'description' => 'Unauthorized',
                    ],
                ],
            ],
        ];
        // }}}
    }

    protected function getPathInfoMap(): array
    {
        // {{{
        $this->registerTag('general');
        $this->registerTag('obsoleted');
        return [
            'get' => [
                'operationId' => 'getMap',
                'summary' => Yii::t('app-apidoc2', 'Get stages (obsoleted)'),
                'description' => implode("\n\n", [
                    Html::encode(Yii::t('app-apidoc2', 'This API has been obsoleted.')),
                    Html::encode(Yii::t('app-apidoc2', 'Use [`{path}`]({link}) instead of this.', [
                        'path' => '/api/v2/stage',
                        'link' => '#operation/getStage',
                    ])),
                ]),
                'tags' => [
                    // 'general',
                    'obsoleted',
                ],
                'responses' => [
                    '301' => [
                        'description' => Yii::t('app-apidoc2', 'Redirect'),
                        'headers' => [
                            'Location' => [
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                                'example' => Url::to(['api-v2/stage'], true),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        // }}}
    }
}
