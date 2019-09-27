<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\CounterAsset;
use yii\base\Widget;
use yii\helpers\Html;

class DigitalCounter extends Widget
{
    public $value;
    public $digits = 0;
    public $type;
    public $options = [
        'class' => 'dseg-counter',
    ];
    public $foregroundOptions = [];

    public function run()
    {
        CounterAsset::register($this->view);

        $value = preg_replace(
            '/[^0-9.:A-Za-z -]/',
            ' ',
            trim((string)$this->value)
        );

        return Html::tag(
            'span',
            implode('', [
                $this->renderBackground($value),
                $this->renderForeground($value),
            ]),
            array_merge(
                ['id' => $this->id],
                $this->options
            )
        );
    }

    public function renderBackground(string $value): string
    {
        $digits = max($this->digits, strlen($value));
        $value = str_repeat('8', $digits);
        $this->view->registerCss(Html::renderCss([
            "#{$this->id}::before" => ($digits > 0)
                ? ['content' => sprintf('"%s"', $value)]
                : ['display' => 'none'],
        ]));

        return '';
    }

    public function renderForeground(string $value): string
    {
        $renderDigits = max($this->digits, strlen($value));
        $padDigits = max(0, $renderDigits - strlen($value));

        if ($padDigits > 0) {
            $this->view->registerCss(Html::renderCss([
                '.dseg-counter-pad' => [
                    'speak' => 'none',
                ],
            ]));
        }

        return Html::tag(
            'span',
            implode('', [
                $padDigits > 0
                    ? Html::tag('span', str_repeat('!', $padDigits), [
                        'class' => 'dseg-counter-pad',
                        'aria-hidden' => 'true',
                    ])
                    : '',
                Html::encode($value),
            ]),
            $this->foregroundOptions
        );
    }
}
