<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\GameModeIconsAsset;
use app\assets\UserMiniinfoAsset;
use app\models\Rank2;
use app\models\Rule2;
use app\models\UserStat2;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

final class UserMiniInfo3 extends Widget
{
    public $id = 'user-miniinfo';
    public $user;

    public function run(): string
    {
        UserMiniinfoAsset::register($this->view);

        $stats = null;
        return Html::tag(
            'div',
            Html::tag(
                'div',
                implode('', [
                    $this->renderHeading(),
                    implode('<hr>', array_filter([
                        $this->renderStatsTotal($stats),
                        $this->renderStatsRegular($stats),
                        $this->renderStatsRanked($stats),
                        $this->renderActivity(),
                    ])),
                ]),
                ['id' => $this->id . '-box']
            ),
            [
                'id' => $this->id,
                'itemprop' => 'author',
                'itemscope' => true,
                'itemtype' => 'http://schema.org/Person',
            ]
        );
    }

    private function renderHeading(): string
    {
        return Html::tag(
            'h2',
            Html::a(
                implode('', [
                    $this->renderUserIcon(),
                    $this->renderUserName(),
                ]),
                ['show-user/profile', 'screen_name' => $this->user->screen_name]
            )
        );
    }

    private function renderUserIcon(): string
    {
        return Html::tag(
            'span',
            UserIcon::widget([
                'user' => $this->user,
                'options' => [
                    'height' => '48',
                    'width' => '48',
                ],
            ]),
            ['class' => 'miniinfo-user-icon']
        );
    }

    private function renderUserName(): string
    {
        return Html::tag(
            'span',
            Html::encode($this->user->name),
            [
                'class' => 'miniinfo-user-name',
                'itemprop' => 'name',
            ]
        );
    }

    private function renderStatsTotal(?UserStat2 $model): ?string
    {
        // {{{
        if (!$model) {
            return null;
        }

        $fmt = Yii::$app->formatter;
        return Html::tag(
            'div',
            DetailView::widget([
                'options' => [
                    'tag' => 'div',
                ],
                'model' => $model,
                'template' => Html::tag(
                    'div',
                    implode('', [
                        Html::tag('div', '{label}', [
                            'class' => 'user-label auto-tooltip',
                            'title' => '{label}',
                        ]),
                        Html::tag('div', '{value}', [
                            'class' => 'user-number',
                        ]),
                    ]),
                    ['class' => 'col-4 col-xs-4']
                ),
                'attributes' => [
                    [
                        'label' => Yii::t('app', 'Battles'),
                        'format' => 'raw',
                        'value' => function (UserStat2 $model) use ($fmt): string {
                            return Html::a(
                                Html::encode($fmt->asInteger($model->battles)),
                                ['show-v3/user', 'screen_name' => $this->user->screen_name]
                            );
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Win %'),
                        'value' => function (UserStat2 $model) use ($fmt): string {
                            return ($model->have_win_lose < 1)
                                ? Yii::t('app', 'N/A')
                                : $fmt->asPercent($model->win_battles / $model->have_win_lose, 1);
                        },
                    ],
                    [
                        'label' => $this->zwsp(),
                        'value' => $this->zwsp(),
                    ],
                    // [
                    //     'label' => Yii::t('app', 'Last {n} Battles', ['n' => 50]),
                    //     'value' => $nbsp,
                    // ],
                    [
                        'label' => Yii::t('app', 'Avg Kills'),
                        'value' => function (UserStat2 $model) use ($fmt): string {
                            return ($model->have_kill_death < 1)
                                ? Yii::t('app', 'N/A')
                                : $fmt->asDecimal($model->kill / $model->have_kill_death, 2);
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Avg Deaths'),
                        'value' => function (UserStat2 $model) use ($fmt): string {
                            return ($model->have_kill_death < 1)
                                ? Yii::t('app', 'N/A')
                                : $fmt->asDecimal($model->death / $model->have_kill_death, 2);
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Kill Ratio'),
                        'value' => function (UserStat2 $model) use ($fmt): string {
                            if ($model->have_kill_death < 1) {
                                return Yii::t('app', 'N/A');
                            }

                            if ($model->death == 0) {
                                if ($model->kill == 0) {
                                    return Yii::t('app', 'N/A');
                                } else {
                                    return $fmt->asDecimal(99.99, 2);
                                }
                            }
                            return $fmt->asDecimal($model->kill / $model->death, 2);
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Kills/min'),
                        'value' => function (UserStat2 $model) use ($fmt): string {
                            return ($model->have_kill_death_time < 1 || $model->total_seconds < 1)
                                ? Yii::t('app', 'N/A')
                                : $fmt->asDecimal(
                                    $model->kill_with_time * 60 / $model->total_seconds,
                                    3
                                );
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Deaths/min'),
                        'value' => function (UserStat2 $model) use ($fmt): string {
                            return ($model->have_kill_death_time < 1 || $model->total_seconds < 1)
                                ? Yii::t('app', 'N/A')
                                : $fmt->asDecimal(
                                    $model->death_with_time * 60 / $model->total_seconds,
                                    3
                                );
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Kill Rate'),
                        'value' => function (UserStat2 $model) use ($fmt): string {
                            if ($model->have_kill_death < 1) {
                                return Yii::t('app', 'N/A');
                            }
                            if ($model->death == 0 && $model->kill == 0) {
                                return Yii::t('app', 'N/A');
                            }
                            return $fmt->asPercent(
                                $model->kill / ($model->kill + $model->death),
                                1
                            );
                        },
                    ],
                ],
            ]),
            ['class' => 'row']
        );
        // }}}
    }

    private function renderStatsRegular(?UserStat2 $model): ?string
    {
        // {{{
        if (!$model) {
            return null;
        }

        $fmt = Yii::$app->formatter;
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::tag(
                        'div',
                        Html::encode(Yii::t('app-rule2', 'Turf War')),
                        ['class' => 'user-label']
                    ),
                    ['class' => 'col-12 col-xs-12']
                ),
                DetailView::widget([
                    'options' => ['tag' => 'div'],
                    'model' => $model,
                    'template' => Html::tag(
                        'div',
                        implode('', [
                            Html::tag('div', '{label}', [
                                'class' => 'user-label auto-tooltip',
                                'title' => '{label}',
                            ]),
                            Html::tag('div', '{value}', ['class' => 'user-number']),
                        ]),
                        ['class' => 'col-4 col-xs-4']
                    ),
                    'attributes' => [
                        [
                            'label' => Yii::t('app', 'Battles'),
                            'format' => 'integer',
                            'attribute' => 'turf_battles',
                        ],
                        [
                            'label' => Yii::t('app', 'Win %'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                return ($model->turf_have_win_lose < 1)
                                    ? Yii::t('app', 'N/A')
                                    : $fmt->asPercent(
                                        $model->turf_win_battles / $model->turf_have_win_lose,
                                        1
                                    );
                            },
                        ],
                        [
                            'label' => $this->zwsp(),
                            'value' => $this->zwsp(),
                        ],
                        // [
                        //     'label' => Yii::t('app', 'Last {n} Battles', ['n' => 50]),
                        //     'value' => $nbsp,
                        // ],
                        [
                            'label' => Yii::t('app', 'Avg Kills'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                return ($model->turf_have_kill_death < 1)
                                    ? Yii::t('app', 'N/A')
                                    : $fmt->asDecimal(
                                        $model->turf_kill / $model->turf_have_kill_death,
                                        2
                                    );
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Avg Deaths'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                return ($model->turf_have_kill_death < 1)
                                    ? Yii::t('app', 'N/A')
                                    : $fmt->asDecimal(
                                        $model->turf_death / $model->turf_have_kill_death,
                                        2
                                    );
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Kill Ratio'),
                            'value' => function ($model) use ($fmt): string {
                                if ($model->turf_have_kill_death < 1) {
                                    return Yii::t('app', 'N/A');
                                }
                                if ($model->turf_death == 0) {
                                    if ($model->turf_kill == 0) {
                                        return Yii::t('app', 'N/A');
                                    } else {
                                        return $fmt->asDecimal(99.99, 2);
                                    }
                                }
                                return $fmt->asDecimal($model->turf_kill / $model->turf_death, 2);
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Total Inked'),
                            'format' => 'raw',
                            'value' => function (UserStat2 $model): string {
                                if ($model->turf_have_inked < 1) {
                                    return Html::encode(Yii::t('app', 'N/A'));
                                }

                                return Html::tag(
                                    'span',
                                    $this->formatShortNumber($model->turf_total_inked),
                                    [
                                        'class' => 'auto-tooltip',
                                        'title' => Yii::t('app', '{point, plural, other{#p}}', [
                                            'point' => $model->turf_total_inked,
                                        ]),
                                    ]
                                );
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Avg Inked'),
                            'value' => function ($model) use ($fmt): string {
                                return ($model->turf_have_inked < 1)
                                    ? Yii::t('app', 'N/A')
                                    : $fmt->asDecimal(
                                        $model->turf_total_inked / $model->turf_have_inked,
                                        1
                                    );
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Max Inked'),
                            'value' => function ($model) use ($fmt): string {
                                return ($model->turf_have_inked < 1)
                                    ? Yii::t('app', 'N/A')
                                    : $fmt->asInteger($model->turf_max_inked);
                            },
                        ],
                    ],
                ]),
            ]),
            ['class' => 'row']
        );
        // }}}
    }

    private function renderStatsRanked(?UserStat2 $model): ?string
    {
        // {{{
        if (!$model) {
            return null;
        }

        $fmt = Yii::$app->formatter;
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::tag(
                        'div',
                        Html::encode(Yii::t('app-rule2', 'Ranked Battle')),
                        ['class' => 'user-label']
                    ),
                    ['class' => 'col-12 col-xs-12']
                ),
                DetailView::widget([
                    'options' => ['tag' => 'div'],
                    'model' => $model,
                    'template' => function ($attribute, $index, $widget): string {
                        // {{{
                        $html = Html::tag(
                            'div',
                            implode('', [
                                Html::tag('div', '{label}', [
                                    'class' => 'user-label auto-tooltip',
                                    'title' => '{label}',
                                ]),
                                Html::tag('div', '{value}', ['class' => 'user-number']),
                            ]),
                            ['class' => 'col-4 col-xs-4']
                        );
                        $captionOptions = Html::renderTagAttributes(
                            ArrayHelper::getValue($attribute, 'captionOptions', [])
                        );
                        $contentOptions = Html::renderTagAttributes(
                            ArrayHelper::getValue($attribute, 'contentOptions', [])
                        );
                        return strtr($html, [
                            '{captionOptions}' => $captionOptions,
                            '{contentOptions}' => $contentOptions,
                            '{label}' => $attribute['label'],
                            '{value}' => $widget->formatter->format(
                                $attribute['value'],
                                $attribute['format']
                            ),
                        ]);
                        // }}}
                    },
                    'attributes' => [
                        [
                            'label' => Yii::t('app', 'Battles'),
                            'format' => 'integer',
                            'attribute' => 'gachi_battles',
                        ],
                        [
                            'label' => Yii::t('app', 'Win %'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                return ($model->gachi_have_win_lose < 1)
                                    ? Yii::t('app', 'N/A')
                                    : $fmt->asPercent(
                                        $model->gachi_win_battles / $model->gachi_have_win_lose,
                                        1
                                    );
                            },
                        ],
                        [
                            'label' => $this->zwsp(),
                            'value' => $this->zwsp(),
                        ],
                        // [
                        //     'label' => Yii::t('app', 'Last {n} Battles', ['n' => 50]),
                        //     'value' => $nbsp,
                        // ],
                        [
                            'label' => Yii::t('app', 'Avg Kills'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                return ($model->gachi_have_kill_death < 1)
                                    ? Yii::t('app', 'N/A')
                                    : $fmt->asDecimal(
                                        $model->gachi_kill / $model->gachi_have_kill_death,
                                        2
                                    );
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Avg Deaths'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                return ($model->gachi_have_kill_death < 1)
                                    ? Yii::t('app', 'N/A')
                                    : $fmt->asDecimal(
                                        $model->gachi_death / $model->gachi_have_kill_death,
                                        2
                                    );
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Kill Ratio'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                if ($model->gachi_have_kill_death < 1) {
                                    return Yii::t('app', 'N/A');
                                }
                                if ($model->gachi_death == 0) {
                                    if ($model->gachi_kill == 0) {
                                        return Yii::t('app', 'N/A');
                                    } else {
                                        return $fmt->asDecimal(99.99, 2);
                                    }
                                }
                                return $fmt->asDecimal($model->gachi_kill / $model->gachi_death, 2);
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Kills/min'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                if (
                                    ($model->gachi_kill_death_time < 1) ||
                                    ($model->gachi_total_seconds < 1)
                                ) {
                                    return Yii::t('app', 'N/A');
                                }

                                return $fmt->asDecimal(
                                    $model->gachi_kill_with_time * 60 / $model->gachi_total_seconds,
                                    3
                                );
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Deaths/min'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                if (
                                    ($model->gachi_kill_death_time < 1) ||
                                    ($model->gachi_total_seconds < 1)
                                ) {
                                    return Yii::t('app', 'N/A');
                                }

                                return $fmt->asDecimal(
                                    $model->gachi_death_with_time * 60 / $model->gachi_total_seconds,
                                    3
                                );
                            },
                        ],
                        [
                            'label' => Yii::t('app', 'Kill Rate'),
                            'value' => function (UserStat2 $model) use ($fmt): string {
                                if ($model->gachi_have_kill_death < 1) {
                                    return Yii::t('app', 'N/A');
                                }
                                if ($model->gachi_death == 0 && $model->gachi_kill == 0) {
                                    return Yii::t('app', 'N/A');
                                }
                                return $fmt->asPercent(
                                    $model->gachi_kill / ($model->gachi_kill + $model->gachi_death),
                                    1
                                );
                            },
                        ],
                    ],
                ]),
                $this->renderStatsRankedCurrent($model),
                $this->renderStatsRankedPeak($model),
            ]),
            ['class' => 'row']
        );
        // }}}
    }

    private function renderStatsRankedCurrent(UserStat2 $model): string
    {
        // {{{
        $am = Yii::$app->assetManager;
        $asset = $am->getBundle(GameModeIconsAsset::class);
        $rules = [
            [
                'attribute' => 'area_current_rank',
                'attributeX' => 'area_current_x_power',
                'icon' => $am->getAssetUrl($asset, 'spl2/area.png'),
                'label' => Yii::t('app', '{rule}: Current', ['rule' => Yii::t('app-rule2', 'SZ')]),
                'ruleName' => Yii::t('app-rule2', 'SZ'),
            ],
            [
                'attribute' => 'yagura_current_rank',
                'attributeX' => 'yagura_current_x_power',
                'icon' => $am->getAssetUrl($asset, 'spl2/yagura.png'),
                'label' => Yii::t('app', '{rule}: Current', ['rule' => Yii::t('app-rule2', 'TC')]),
                'ruleName' => Yii::t('app-rule2', 'TC'),
            ],
            [
                'attribute' => 'hoko_current_rank',
                'attributeX' => 'hoko_current_x_power',
                'icon' => $am->getAssetUrl($asset, 'spl2/hoko.png'),
                'label' => Yii::t('app', '{rule}: Current', ['rule' => Yii::t('app-rule2', 'RM')]),
                'ruleName' => Yii::t('app-rule2', 'RM'),
            ],
            [
                'attribute' => 'asari_current_rank',
                'attributeX' => 'asari_current_x_power',
                'icon' => $am->getAssetUrl($asset, 'spl2/asari.png'),
                'label' => Yii::t('app', '{rule}: Current', ['rule' => Yii::t('app-rule2', 'CB')]),
                'ruleName' => Yii::t('app-rule2', 'CB'),
            ],
        ];
        $rows = [
            Html::tag(
                'div',
                Html::tag(
                    'div',
                    Html::encode(Yii::t('app', 'Rank: Current')),
                    ['class' => 'user-label']
                ),
                ['class' => 'col-12 col-xs-12']
            ),
            DetailView::widget([
                'options' => ['tag' => 'div'],
                'model' => $model,
                'template' => function ($attribute, $index, $widget): string {
                    // {{{
                    $html = Html::tag(
                        'div',
                        implode('', [
                            Html::tag('div', '{label}', [
                                'class' => 'user-label',
                            ]),
                            Html::tag('div', '{value}', ['class' => 'user-number']),
                        ]),
                        ['class' => 'col-3 col-xs-3']
                    );
                    $captionOptions = Html::renderTagAttributes(
                        ArrayHelper::getValue($attribute, 'captionOptions', [])
                    );
                    $contentOptions = Html::renderTagAttributes(
                        ArrayHelper::getValue($attribute, 'contentOptions', [])
                    );
                    return strtr($html, [
                        '{captionOptions}' => $captionOptions,
                        '{contentOptions}' => $contentOptions,
                        '{label}' => $attribute['label'],
                        '{value}' => $widget->formatter->format(
                            $attribute['value'],
                            $attribute['format']
                        ),
                    ]);
                    // }}}
                },
                'attributes' => array_map(
                    function (array $info): array {
                        return [
                            'attribute' => $info['attribute'],
                            'label' => Html::img($info['icon'], [
                                'alt' => $info['ruleName'],
                                'class' => 'auto-tooltip',
                                'title' => $info['label'],
                            ]),
                            'format' => 'raw',
                            'value' => function (UserStat2 $model) use ($info): string {
                                $value = $model->{$info['attribute']};
                                if ($model->gachi_battles < 1 || $value === null) {
                                    return Html::encode(Yii::t('app', 'N/A'));
                                }

                                $html = Html::rank2($value);
                                if (!$html) {
                                    return Html::encode(Yii::t('app', 'N/A'));
                                }

                                return Html::tag('span', $html, ['class' => 'nobr']);
                            },
                        ];
                    },
                    $rules
                ),
            ]),
        ];

        $xPowerAvailable = count(array_filter($rules, function (array $rule) use ($model): bool {
            return $model->{$rule['attributeX']} > 0;
        }));
        if ($xPowerAvailable > 0) {
            $rows[] = implode('', array_map(
                function (array $rule) use ($model): string {
                    return Html::tag(
                        'div',
                        ($model->{$rule['attributeX']} > 0)
                            ? Html::tag('small', Html::encode(
                                Yii::$app->formatter->asDecimal($model->{$rule['attributeX']}, 1)
                            ))
                            : '&#8203;',
                        ['class' => 'col-3 col-xs-3 nobr']
                    );
                },
                $rules
            ));
        }

        return implode('', $rows);
        // }}}
    }

    private function renderStatsRankedPeak(UserStat2 $model): string
    {
        // {{{
        $am = Yii::$app->assetManager;
        $asset = $am->getBundle(GameModeIconsAsset::class);
        $rules = [
            [
                'attribute' => 'area_rank_peak',
                'attributeX' => 'area_x_power_peak',
                'icon' => $am->getAssetUrl($asset, 'spl2/area.png'),
                'label' => Yii::t('app', '{rule}: Peak', ['rule' => Yii::t('app-rule2', 'SZ')]),
                'ruleName' => Yii::t('app-rule2', 'SZ'),
            ],
            [
                'attribute' => 'yagura_rank_peak',
                'attributeX' => 'yagura_x_power_peak',
                'icon' => $am->getAssetUrl($asset, 'spl2/yagura.png'),
                'label' => Yii::t('app', '{rule}: Peak', ['rule' => Yii::t('app-rule2', 'TC')]),
                'ruleName' => Yii::t('app-rule2', 'TC'),
            ],
            [
                'attribute' => 'hoko_rank_peak',
                'attributeX' => 'hoko_x_power_peak',
                'icon' => $am->getAssetUrl($asset, 'spl2/hoko.png'),
                'label' => Yii::t('app', '{rule}: Peak', ['rule' => Yii::t('app-rule2', 'RM')]),
                'ruleName' => Yii::t('app-rule2', 'RM'),
            ],
            [
                'attribute' => 'asari_rank_peak',
                'attributeX' => 'asari_x_power_peak',
                'icon' => $am->getAssetUrl($asset, 'spl2/asari.png'),
                'label' => Yii::t('app', '{rule}: Peak', ['rule' => Yii::t('app-rule2', 'CB')]),
                'ruleName' => Yii::t('app-rule2', 'CB'),
            ],
        ];
        $rows = [
            Html::tag(
                'div',
                Html::tag(
                    'div',
                    Html::encode(Yii::t('app', 'Rank: Peak')),
                    ['class' => 'user-label']
                ),
                ['class' => 'col-12 col-xs-12']
            ),
            DetailView::widget([
                'options' => ['tag' => 'div'],
                'model' => $model,
                'template' => function ($attribute, $index, $widget): string {
                    // {{{
                    $html = Html::tag(
                        'div',
                        implode('', [
                            Html::tag('div', '{label}', [
                                'class' => 'user-label',
                            ]),
                            Html::tag('div', '{value}', ['class' => 'user-number']),
                        ]),
                        ['class' => 'col-3 col-xs-3']
                    );
                    $captionOptions = Html::renderTagAttributes(
                        ArrayHelper::getValue($attribute, 'captionOptions', [])
                    );
                    $contentOptions = Html::renderTagAttributes(
                        ArrayHelper::getValue($attribute, 'contentOptions', [])
                    );
                    return strtr($html, [
                        '{captionOptions}' => $captionOptions,
                        '{contentOptions}' => $contentOptions,
                        '{label}' => $attribute['label'],
                        '{value}' => $widget->formatter->format(
                            $attribute['value'],
                            $attribute['format']
                        ),
                    ]);
                    // }}}
                },
                'attributes' => array_map(
                    function (array $info): array {
                        return [
                            'attribute' => $info['attribute'],
                            'label' => Html::img($info['icon'], [
                                'alt' => $info['ruleName'],
                                'class' => 'auto-tooltip',
                                'title' => $info['label'],
                            ]),
                            'format' => 'raw',
                            'value' => function (UserStat2 $model) use ($info): string {
                                $value = $model->{$info['attribute']};
                                if ($model->gachi_battles < 1 || $value === null) {
                                    return Html::encode(Yii::t('app', 'N/A'));
                                }

                                $html = Html::rank2($value);
                                if (!$html) {
                                    return Html::encode(Yii::t('app', 'N/A'));
                                }

                                return Html::tag('span', $html, ['class' => 'nobr']);
                            },
                        ];
                    },
                    $rules
                ),
            ]),
        ];

        $xPowerAvailable = count(array_filter($rules, function (array $rule) use ($model): bool {
            return $model->{$rule['attributeX']} > 0;
        }));
        if ($xPowerAvailable > 0) {
            $rows[] = implode('', array_map(
                function (array $rule) use ($model): string {
                    return Html::tag(
                        'div',
                        ($model->{$rule['attributeX']} > 0)
                            ? Html::tag('small', Html::encode(
                                Yii::$app->formatter->asDecimal($model->{$rule['attributeX']}, 1)
                            ))
                            : '&#8203;',
                        ['class' => 'col-3 col-xs-3 nobr']
                    );
                },
                $rules
            ));
        }

        return implode('', $rows);
        // }}}
    }

    private function renderActivity(): string
    {
        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::encode(Yii::t('app', 'Activity')),
                    ['class' => 'label-user']
                ),
                Html::tag(
                    'div',
                    ActivityWidget::widget([
                        'user' => $this->user,
                        'months' => 4,
                        'longLabel' => false,
                        'size' => 9,
                        'only' => 'spl3',
                    ]),
                    ['class' => 'table-responsive bg-white']
                ),
            ]),
            ['class' => 'miniinfo-databox']
        );
    }

    private function zwsp(): string
    {
        // U+200B
        return mb_chr(0x200b, 'UTF-8');
    }

    private function formatShortNumber(int $value): string
    {
        $lang = strtolower(Yii::$app->language);
        if (substr($lang, 0, 3) === 'ja-') {
            return $this->formatShortNumberJapanese($value);
        } elseif (substr($lang, 0, 3) === 'ko-') {
            return $this->formatShortNumberKorean($value);
        } elseif ($lang === 'zh-cn') {
            return $this->formatShortNumberChineseS($value);
        } elseif ($lang === 'zh-tw') {
            return $this->formatShortNumberChineseT($value);
        } else {
            return $this->formatShortNumberDefault($value);
        }
    }

    private function formatShortNumberJapanese(int $value): string
    {
        return $this->formatShortNumberImpl($value, 4, [
            '万', '億', '兆', '京', '垓', '𥝱', '穣'
        ]);
    }

    private function formatShortNumberKorean(int $value): string
    {
        return $this->formatShortNumberDefault($value);
    }

    private function formatShortNumberChineseS(int $value): string
    {
        return $this->formatShortNumberImpl($value, 4, [
            '万', '亿', '兆', '京', '垓', '秭', '穰'
        ]);
    }

    private function formatShortNumberChineseT(int $value): string
    {
        return $this->formatShortNumberImpl($value, 4, [
            '萬', '億', '兆', '京', '垓', '秭', '穰'
        ]);
    }

    private function formatShortNumberDefault(int $value): string
    {
        return $this->formatShortNumberImpl($value, 3, [
            'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y',
        ]);
    }

    private function formatShortNumberImpl(int $value, int $power, array $units): string
    {
        $formatted = Yii::$app->formatter->asInteger($value);
        foreach ($units as $i => $unit) {
            $base = (int)pow(10, ($i + 1) * $power);
            if ($value < $base) {
                break;
            }
            $formatted = implode('', [
                Html::encode((string)(int)($value / $base)),
                Html::tag('small', '&nbsp;' . Html::encode($unit), ['style' => 'font-size:50%']),
            ]);
        }

        return $formatted;
    }
}
