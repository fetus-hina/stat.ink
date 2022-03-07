<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\FontAwesomeAsset;
use app\components\helpers\Html;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;

class WashHandsWidget extends Widget
{
    public function run()
    {
        FontAwesomeAsset::register($this->view);
        BootstrapAsset::register($this->view);

        return Html::tag(
            'div',
            Html::tag(
                'div',
                implode('', [
                    $this->renderLeftIcon(),
                    $this->renderText(),
                    $this->renderRightIcon(),
                ]),
                ['class' => 'd-flex align-items-center']
            ),
            [
                'class' => 'alert alert-info mb-3',
                'id' => $this->id,
            ]
        );
    }

    private function renderLeftIcon(): string
    {
        return $this->renderIcon('fas', 'pump-soap', 'mr-3');
    }

    private function renderRightIcon(): string
    {
        return $this->renderIcon('fas', 'hands-wash', null);
    }

    private function renderIcon(string $faCategory, string $faIcon, ?string $wrapperClass): string
    {
        $icon = call_user_func([FA::class, $faCategory], $faIcon)->fw();
        $this->view->registerCss(implode('', [
            Html::renderCss([
                '#' . $icon->id => [
                    'font-size' => '4em',
                ],
            ]),
            sprintf('@media (min-width:768px){%s}', Html::renderCss([
                '#' . $icon->id => [
                    'font-size' => '5em',
                ],
            ])),
        ]));
        return Html::tag('span', (string)$icon, [
            'class' => [
                $wrapperClass,
                'flex-grow-0',
                'flex-shrink-0',
            ],
        ]);
    }

    private function renderText(): string
    {
        $id = $this->id . '-text';
        $this->view->registerCss(implode('', [
            Html::renderCss([
                '#' . $id => [
                    'font-size' => '32px',
                ],
            ]),
            sprintf('@media (min-width:768px){%s}', Html::renderCss([
                '#' . $id => [
                    'font-size' => '40px',
                ],
            ])),
        ]));

        return Html::tag(
            'span',
            Html::encode(Yii::t('app', 'Wash your hands to stay fresh!')),
            [
                'class' => [
                    'paintball',
                    'text-center',
                    'mr-3',
                    'flex-grow-1',
                    'flex-shrink-1',
                ],
                'id' => $id,
            ]
        );
    }
}
