<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\assets\FlexboxAsset;
use app\components\widgets\v3\salmonJob\Block;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

use function implode;
use function vsprintf;

final class SalmonJobPoint extends Widget
{
    public int $jobPoint;
    public int $jobScore;
    public float $jobRate;
    public int $jobBonus;

    public function run(): string
    {
        $view = $this->view;
        if ($view instanceof View) {
            FlexboxAsset::register($view);
            $view->registerCss(vsprintf('#%s small{%s}', [
                (string)$this->id,
                'font-size:80%',
            ]));
        }

        return Html::tag(
            'div',
            implode('', [
                Block::widget(['value' => $this->jobPoint]),
                Block::widget(['value' => '=']),
                Block::widget([
                    'value' => $this->jobScore,
                    'label' => Yii::t('app-salmon3', 'Job Score'),
                    'format' => 'integer',
                ]),
                Block::widget(['value' => '×']),
                Block::widget([
                    'value' => $this->jobRate,
                    'label' => Yii::t('app-salmon3', 'Pay Grade'),
                    'format' => ['decimal', 2],
                ]),
                Block::widget(['value' => '+']),
                Block::widget([
                    'value' => $this->jobBonus,
                    'label' => Yii::t('app-salmon3', 'Clear Bonus'),
                    'format' => 'integer',
                ]),
            ]),
            [
                'class' => 'd-flex',
                'id' => (string)$this->id,
            ],
        );
    }
}
