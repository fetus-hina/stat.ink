<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\salmonJob;

use Yii;
use app\assets\FlexboxAsset;
use app\components\widgets\v3\salmonJob\Block;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

final class Block extends Widget
{
    /**
     * @var int|float
     */
    public $value;

    public ?string $label = null;

    /**
     * @var string|array
     */
    public $format = 'text';

    public function run(): string
    {
        $view = $this->view;
        if ($view instanceof View) {
            FlexboxAsset::register($this->view);
        }

        return Html::tag(
            'div',
            \implode('', [
                $this->renderLabel(),
                $this->renderValue(),
            ]),
            [
                'class' => [
                    'd-flex',
                    'flex-column-reverse',
                    'justify-content-end',
                ],
            ],
        );
    }

    private function renderLabel(): string
    {
        $label = \trim((string)$this->label);
        if ($label === '') {
            return '';
        }

        return Html::tag('small', Html::encode($label), [
            'class' => [
                'd-none',
                'd-sm-block',
                'px-1',
                'text-center',
                'text-muted',
            ],
        ]);
    }

    private function renderValue(): string
    {
        $value = Yii::$app->formatter->format($this->value, $this->format);
        return Html::tag('div', $value, [
            'class' => [
                'pl-1',
                'pr-1',
                'text-center',
            ],
        ]);
    }
}
