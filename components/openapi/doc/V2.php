<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\openapi\doc;

use Yii;
use app\models\Gear2;
use app\models\Language;
use app\models\Map2;
use app\models\Mode2;
use app\models\Special2;
use app\models\Subweapon2;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use app\models\WeaponType2;
use app\models\openapi\SplatNet2ID;
use app\models\openapi\Util as OpenApiUtil;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
            '/api/v2/rule' => $this->getPathInfoMode(),
            '/api/v2/stage' => $this->getPathInfoStage(),
            '/api/v2/weapon' => $this->getPathInfoWeapon(),
            '/api/v2/weapon.csv' => $this->getPathInfoWeaponCsv(),
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
}
