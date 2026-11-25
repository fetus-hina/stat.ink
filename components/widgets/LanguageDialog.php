<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use Override;
use Yii;
use app\assets\LanguageDialogAsset;
use app\models\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function array_map;
use function array_merge;
use function implode;
use function preg_match;
use function sprintf;
use function strcmp;
use function strtolower;
use function vsprintf;

final class LanguageDialog extends Dialog
{
    #[Override]
    public function init(): void
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
                [$this->createSeparator()],
                $this->createLinkList(),
            )),
            ['class' => 'list-group-flush'],
        );
    }

    /**
     * @return string[]
     */
    private function createLanguageList(): array
    {
        LanguageDialogAsset::register($this->view);

        return Yii::$app->cache->getOrSet(
            [
                __METHOD__,
                Yii::$app->getLocale(),
                ArrayHelper::getValue(Yii::$app->params, 'assetRevision'),
            ],
            fn (): array => array_map(
                fn (Language $lang): string => match ($lang->lang) {
                    Yii::$app->getLocale() => Html::tag(
                        'div',
                        $this->renderLanguageItem($lang),
                        [
                            'class' => [
                                'list-group-item',
                                'current',
                            ],
                        ],
                    ),
                    default => Html::a(
                        $this->renderLanguageItem($lang),
                        null,
                        [
                            'class' => [
                                'list-group-item',
                                'language-change',
                                'cursor-pointer',
                            ],
                            'data' => [
                                'lang' => $lang->lang,
                            ],
                            'hreflang' => $lang->lang,
                        ],
                    ),
                },
                ArrayHelper::sort(
                    Language::find()
                        ->cache(86400)
                        ->all(),
                    fn (Language $a, Language $b): int => strcmp($a->name, $b->name),
                ),
            ),
            86400,
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

        return Html::tag('span', implode('', [
            $this->flagIcon(strtolower($lang->countryCode), strtolower($lang->languageCode)),
            $label,
        ]));
    }

    private function createSeparator(): string
    {
        return Html::tag('div', '', [
            'class' => [
                'list-group-item',
                'separator',
            ],
            'role' => 'separator',
        ]);
    }

    /**
     * @return string[]
     */
    private function createLinkList(): array
    {
        return [
            Html::a(
                implode(' ', [
                    Icon::help(),
                    Html::encode(Yii::t('app-translate', 'About Translation')),
                ]),
                ['site/translate'],
                ['class' => 'list-group-item'],
            ),
            Html::a(
                implode(' ', [
                    Icon::refresh(),
                    Html::encode(Yii::t('app-translate', 'How to update the translation files')),
                ]),
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
                    (string)FlagIcon::fg('gb'),
                    ['class' => 'mr-1'],
                ),
                Html::tag(
                    'span',
                    (string)FlagIcon::fg('au'),
                    ['class' => 'mr-1'],
                ),
            ]);
        }

        if ($countryCode === 'tw') {
            return implode('', [
                Html::tag(
                    'span',
                    (string)FlagIcon::fg('tw'),
                    ['class' => 'mr-1'],
                ),
                Html::tag(
                    'span',
                    (string)FlagIcon::fg('hk'),
                    ['class' => 'mr-1'],
                ),
            ]);
        }

        return Html::tag(
            'span',
            (string)($countryCode |> strtolower(...) |> FlagIcon::fg(...)),
            ['class' => 'mr-1'],
        );
    }
}
