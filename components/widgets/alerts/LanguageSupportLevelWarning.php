<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use Yii;
use app\assets\FlagIconCssAsset;
use app\components\widgets\Alert;
use app\components\widgets\FA;
use app\models\Language;
use app\models\SupportLevel;
use yii\base\Widget;
use yii\helpers\Html;

class LanguageSupportLevelWarning extends Widget
{
    private $language;

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
            [SupportLevel::PARTIAL, SupportLevel::FEW],
            true
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
                    Html::encode('This language support is really limited at this time.')
                ),
                Html::tag(
                    'p',
                    Html::encode('Only proper nouns translated. (e.g. weapons, stages)')
                ),
                Html::tag(
                    'p',
                    Html::a(
                        Html::encode('We need your support!'),
                        'https://github.com/fetus-hina/stat.ink/wiki/Translation',
                        ['class' => 'alert-link']
                    )
                ),
            ]),
        ]);
    }

    protected function renderCurrentLanguage(): string
    {
        if (!$this->language) {
            return '';
        }

        FlagIconCssAsset::register($this->view);

        return Html::tag('p', implode(' ', [
            Html::tag('strong', implode('', [
                Html::encode('Language: '),
                Html::tag('span', '', [
                    'class' => [
                        'flag-icon',
                        'flag-icon-' . $this->language->countryCode,
                    ],
                ]),
                ' ',
                Html::encode($this->language->name),
            ])),
            Html::button(
                FA::fas('language') . ' ' . Html::encode('Switch'),
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
                ]
            ),
        ]));
    }
}
