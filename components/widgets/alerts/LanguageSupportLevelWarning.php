<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use Yii;
use app\assets\LanguageDialogAsset;
use app\components\widgets\Alert;
use app\components\widgets\FlagIcon;
use app\components\widgets\Icon;
use app\models\Language;
use app\models\SupportLevel;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;
use function in_array;
use function strtolower;

class LanguageSupportLevelWarning extends Widget
{
    private ?Language $language = null;

    public function init()
    {
        parent::init();
        $this->language = Language::findOne([
            'lang' => Yii::$app->language,
        ]);
    }

    public function run()
    {
        if (!$this->language) {
            return $this->renderAlert();
        }

        $needsRender = in_array(
            (int)$this->language->support_level_id,
            [SupportLevel::PARTIAL, SupportLevel::FEW, SupportLevel::MACHINE],
            true,
        );
        if ($needsRender) {
            return $this->renderAlert();
        }

        return '';
    }

    protected function renderAlert(): string
    {
        return Alert::widget([
            'options' => [
                'class' => ['alert-danger'],
            ],
            'body' => implode('', [
                $this->renderCurrentLanguage(),
                Html::tag(
                    'p',
                    Html::encode('This language support is really limited at this time.'),
                ),
                in_array(
                    (int)$this->language->support_level_id,
                    [SupportLevel::ALMOST, SupportLevel::PARTIAL],
                    true,
                )
                    ? ''
                    : Html::tag(
                        'p',
                        Html::encode(
                            (int)$this->language->support_level_id === SupportLevel::MACHINE
                                ? 'Almost every text is machine translated.'
                                : 'Only proper nouns (e.g., weapons, stages) translated.',
                        ),
                    ),
                Html::tag(
                    'p',
                    Html::a(
                        Html::encode('We need your support!'),
                        'https://github.com/fetus-hina/stat.ink/wiki/Translation',
                        ['class' => 'alert-link'],
                    ),
                ),
                $this->renderMachineTranslate(),
            ]),
        ]);
    }

    protected function renderCurrentLanguage(): string
    {
        if (!$this->language) {
            return '';
        }

        return Html::tag('p', implode(' ', [
            Html::tag('strong', implode('', [
                Html::encode('Language: '),
                (string)FlagIcon::fg(strtolower($this->language->countryCode)),
                ' ',
                Html::encode($this->language->name),
            ])),
            Html::button(
                implode(' ', [
                    Icon::language(),
                    Html::encode('Switch'),
                ]),
                [
                    'class' => [
                        'btn',
                        'btn-default',
                        'btn-xs',
                    ],
                    'data' => [
                        'toggle' => 'modal',
                        'target' => '#language-dialog',
                    ],
                    'aria-role' => 'button',
                ],
            ),
        ]));
    }

    protected function renderMachineTranslate(): string
    {
        if (!$this->language) {
            return '';
        }

        if (
            in_array(
                $this->language->lang,
                ['ko-KR', 'pt-BR'], // Machine translation is not available
                true,
            )
        ) {
            return '';
        }

        if (Yii::$app->isEnabledMachineTranslation) {
            return Html::tag('p', implode(' ', [
                Html::encode('Currently displayed using machine translation (Powered by DeepL).'),
                $this->renderMachineTranslateSwitch(false),
            ]));
        } else {
            return Html::tag('p', implode(' ', [
                Html::encode('The machine translation option is currently disabled.'),
                $this->renderMachineTranslateSwitch(true),
            ]));
        }
    }

    protected function renderMachineTranslateSwitch(bool $toEnable): string
    {
        if (!$this->language) {
            return '';
        }

        LanguageDialogAsset::register($this->view);

        return Html::tag(
            'a',
            Html::encode($toEnable ? 'Enable machine-translation' : 'Disable machine-translation'),
            [
                'class' => [
                    'btn',
                    'btn-default',
                    'btn-xs',
                    'language-change-machine-translation',
                ],
                'data' => [
                    'direction' => $toEnable ? 'enable' : 'disable',
                ],
                'aria-role' => 'button',
            ],
        );
    }
}
