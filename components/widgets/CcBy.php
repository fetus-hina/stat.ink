<?php

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;

final class CcBy extends Widget
{
    public function run(): string
    {
        return Html::tag('p', \implode('<br>', [
            Html::img('@web/static-assets/cc/cc-by.svg', [
                'alt' => 'CC-BY 4.0',
            ]),
            Yii::t(
                'app',
                'This document is under a <a href="http://creativecommons.org/licenses/by/4.0/deed.en">Creative Commons Attribution 4.0 International License</a>.',
            ),
        ]));
    }
}
