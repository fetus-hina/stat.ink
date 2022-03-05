<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\FlexboxAsset;
use app\assets\LanguageDialogAsset;
use app\models\Language;
use app\models\SupportLevel;
use yii\helpers\Html;

use const SORT_ASC;

class LanguageDialog extends Dialog
{
    public function init()
    {
        parent::init();

        $this->title = implode(' ', [
            FA::fas('language')->fw(),
            Html::encode('Choose your language'),
        ]);
        $this->titleFormat = 'raw';
        $this->hasClose = true;
        $this->footer = Dialog::FOOTER_CLOSE;
        $this->body = $this->createBody();
        $this->wrapBody = false;
    }

    private function createBody(): string
    {
        return Html::tag(
            'div',
            implode('', array_merge(
                $this->createLanguageList(),
                $this->createHintList()
            )),
            ['class' => 'list-group-flush']
        );
    }

    private function createLanguageList(): array
    {
        return array_map(
            function (Language $lang): string {
                if ($lang->lang === Yii::$app->locale) {
                    return Html::tag('div', $this->renderLanguageItem($lang), [
                        'class' => [
                            'list-group-item',
                            'current',
                        ],
                    ]);
                } else {
                    LanguageDialogAsset::register($this->view);
                    return Html::a($this->renderLanguageItem($lang), null, [
                        'class' => [
                            'list-group-item',
                            'language-change',
                            'cursor-pointer',
                        ],
                        'data' => [
                            'lang' => $lang->lang,
                        ],
                        'hreflang' => $lang->lang,
                    ]);
                }
            },
            Language::find()->with('supportLevel')->orderBy(['name' => SORT_ASC])->all()
        );
    }

    private function renderLanguageItem(Language $lang): string
    {
        $flag = Html::tag(
            'span',
            FlagIcon::fg(strtolower($lang->countryCode)),
            ['class' => 'mr-1']
        );

        $label = $lang->name === $lang->name_en
            ? $lang->name
            : sprintf('%s / %s', $lang->name, $lang->name_en);

        $levelIcon = $this->renderSupportLevelIcon($lang->supportLevel);

        $left = Html::tag('span', implode('', [
            $flag,
            Html::encode($label),
        ]));

        $right = Html::tag('span', implode('', [
            $levelIcon,
        ]));

        FlexboxAsset::register($this->view);
        return Html::tag(
            'div',
            $left . $right,
            [
                'class' => [
                    'd-flex',
                    'justify-content-between',
                ],
            ]
        );
    }

    private function renderSupportLevelIcon(SupportLevel $level): string
    {
        switch ($level->id) {
            case SupportLevel::FULL:
            case SupportLevel::ALMOST:
            default:
                return FA::fas(null)->fw()->__toString();

            case SupportLevel::PARTIAL:
                return FA::fas(
                    'exclamation-circle',
                    [
                        'options' => [
                            'class' => 'auto-tooltip',
                            'title' => 'Partially supported',
                        ],
                    ]
                )
                ->fw()
                ->__toString();

            case SupportLevel::FEW:
                return FA::fas(
                    'exclamation-triangle',
                    [
                        'options' => [
                            'class' => 'auto-tooltip',
                            'title' => 'Proper-noun only',
                        ],
                    ]
                )
                ->fw()
                ->__toString();

            case SupportLevel::MACHINE:
                return FA::fas(
                    'robot',
                    [
                        'options' => [
                            'class' => 'auto-tooltip',
                            'title' => 'Machine-translated',
                        ],
                    ]
                )
                ->fw()
                ->__toString();
        }
    }

    private function createHintList(): array
    {
        $enabledMachineTranslation = Yii::$app->isEnabledMachineTranslation;

        return [
            Html::tag(
                'div',
                implode('', [
                    Html::tag(
                        'a',
                        implode(' ', [
                            $enabledMachineTranslation
                                ? FA::far('check-square')->fw()
                                : FA::far('square')->fw(),
                            Html::encode(Yii::t('app', 'Enable machine-translation')),
                        ]),
                        [
                            'rel' => 'nofollow',
                            'class' => [
                                'language-change-machine-translation',
                                'cursor-pointer',
                            ],
                            'data' => [
                                'direction' => $enabledMachineTranslation ? 'disable' : 'enable',
                            ],
                        ]
                    ),
                    Html::tag(
                        'div',
                        implode('<br>', [
                            FA::fas('exclamation-circle')->fw() . ' : Partically supported',
                            FA::fas('exclamation-triangle')->fw() . ' : Proper-noun only',
                            FA::fas('robot')->fw() . ' : Almost machine-translated',
                        ]),
                        [
                            'class' => 'ml-auto',
                        ]
                    ),
                ]),
                [
                    'class' => [
                        'list-group-item',
                        'hint',
                        'd-flex',
                    ],
                ]
            ),
            Html::a(
                FA::fas('question-circle')->fw() . ' About Translation',
                ['site/translate'],
                ['class' => 'list-group-item']
            ),
            Html::a(
                FA::fas('sync')->fw() . ' How to update',
                'https://github.com/fetus-hina/stat.ink/wiki/Translation',
                ['class' => 'list-group-item']
            ),
        ];
    }
}
