<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
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
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

final class LanguageDialog extends Dialog
{
    public function init()
    {
        parent::init();

        $this->title = implode(' ', [
            Icon::language(),
            Html::tag(
                'span',
                Html::encode('Choose your language'),
                [
                    'class' => 'lang-en lang-en-us',
                    'lang' => 'en-US',
                ],
            ),
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
                $this->createHintList(),
            )),
            ['class' => 'list-group-flush'],
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
            ArrayHelper::sort(
                Language::find()->with('supportLevel')->all(),
                fn (Language $a, Language $b): int => strcmp($a->name, $b->name)
            ),
        );
    }

    private function renderLanguageItem(Language $lang): string
    {
        $label = $lang->name === $lang->name_en
            ? $this->langText($lang->name, 'en-US')
            : vsprintf('%s / %s', [
                $this->langText($lang->name, $lang->lang),
                $this->langText($lang->name_en, 'en-US'),
            ]);

        $levelIcon = $this->renderSupportLevelIcon($lang->supportLevel);

        $left = Html::tag('span', implode('', [
            $this->flagIcon(strtolower($lang->countryCode), strtolower($lang->languageCode)),
            $label,
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
            ],
        );
    }

    private function renderSupportLevelIcon(SupportLevel $level): string
    {
        switch ($level->id) {
            case SupportLevel::FULL:
            case SupportLevel::ALMOST:
            default:
                return Icon::dummy();

            case SupportLevel::PARTIAL:
                return Html::tag('span', Icon::languageLevelPartical(), [
                    'class' => 'auto-tooltip',
                    'title' => 'Partically supported',
                ]);

            case SupportLevel::FEW:
                return Html::tag('span', Icon::languageLevelFew(), [
                    'class' => 'auto-tooltip',
                    'title' => 'Proper-noun only',
                ]);

            case SupportLevel::MACHINE:
                return Html::tag('span', Icon::languageLevelMachine(), [
                    'class' => 'auto-tooltip',
                    'title' => 'Machine-translated',
                ]);
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
                                ? Icon::checkboxChecked()
                                : Icon::checkboxEmpty(),
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
                        ],
                    ),
                    Html::tag(
                        'div',
                        \implode('<br>', [
                            Icon::languageLevelPartical() . ' : Partically supported',
                            Icon::languageLevelFew() . ' : Proper-noun only',
                            Icon::languageLevelMachine() . ' : Almost machine-translated',
                        ]),
                        [
                            'class' => 'ml-auto',
                        ],
                    ),
                ]),
                [
                    'class' => [
                        'list-group-item',
                        'hint',
                        'd-flex',
                    ],
                ],
            ),
            Html::a(
                Icon::help() . ' About Translation',
                ['site/translate'],
                ['class' => 'list-group-item'],
            ),
            Html::a(
                Icon::refresh() . ' How to update',
                'https://github.com/fetus-hina/stat.ink/wiki/Translation',
                ['class' => 'list-group-item'],
            ),
        ];
    }

    private function langText(string $text, string $langCode): string
    {
        if (!preg_match('/^([a-z]+)-([a-z]+)/i', $langCode, $match)) {
            return Html::encode($text);
        }

        [, $codeLang, $codeRegion] = $match;

        return Html::tag(
            'span',
            Html::encode($text),
            [
                'lang' => sprintf('%s-%s', $codeLang, $codeRegion),
                'class' => [
                    sprintf('lang-%s', strtolower($codeLang)),
                    sprintf('lang-%s-%s', strtolower($codeLang), strtolower($codeRegion)),
                ],
            ],
        );
    }

    private function flagIcon(string $countryCode, string $languageCode): string
    {
        if ($countryCode === 'gb') {
            return implode('', [
                Html::tag(
                    'span',
                    FlagIcon::fg('gb'),
                    ['class' => 'mr-1'],
                ),
                Html::tag(
                    'span',
                    FlagIcon::fg('au'),
                    ['class' => 'mr-1'],
                ),
            ]);
        }

        if ($countryCode === 'tw') {
            return implode('', [
                Html::tag(
                    'span',
                    FlagIcon::fg('tw'),
                    ['class' => 'mr-1'],
                ),
                Html::tag(
                    'span',
                    FlagIcon::fg('hk'),
                    ['class' => 'mr-1'],
                ),
            ]);
        }

        return Html::tag(
            'span',
            FlagIcon::fg(strtolower($countryCode)),
            ['class' => 'mr-1'],
        );
    }
}
