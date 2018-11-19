<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
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
    public $backgroundOptions = [
        'class' => 'dseg-counter-bg',
        'aria-hidden' => 'true',
        'style' => [
            'speak' => 'none',
        ],
    ];

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
        return Html::tag(
            'span',
            Html::encode(str_repeat('8', $digits)),
            $this->backgroundOptions
        );
    }

    public function renderForeground(string $value): string
    {
        return Html::tag(
            'span',
            Html::encode($value),
            $this->foregroundOptions
        );
    }
}
