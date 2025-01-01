<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\RatioAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, array{battles: int, wins: int}> $data
 */

ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
RatioAsset::register($this);

$className = 'chart-' . hash('crc32b', __FILE__);

$this->registerJs("
  jQuery('.{$className}').each(
    function () {
      const elem = this;
      const config = JSON.parse(this.getAttribute('data-chart'));
      const canvas = elem.appendChild(document.createElement('canvas'));
      new window.Chart(canvas.getContext('2d'), config);
    }
  );
");

?>
<div class="ratio ratio-16x9">
  <?= Html::tag('div', '', [
    'class' => $className,
    'data' => [
      'chart' => [
        'data' => [
          'datasets' => [
            require __DIR__ . '/chart/winpct.php',
            require __DIR__ . '/chart/error-bar.php',
          ],
        ],
        'options' => [
          'aspectRatio' => 16 / 9,
          'animation' => [
            'duration' => 0,
          ],
          'plugins' => [
            'legend' => [
              'display' => false,
            ],
            'tooltip' => [
              'enabled' => false,
            ],
          ],
          'scales' => [
            'x' => [
              'grid' => [
                 'offset' => false,
              ],
              'offset' => true,
              'title' => [
                'display' => true,
                'text' => Yii::t('app', 'Special Uses'),
              ],
              'type' => 'linear',
              'ticks' => [
                'precision' => 0,
                'stepSize' => 1,
              ],
            ],
            'y' => [
              'max' => 100,
              'min' => 0,
              'title' => [
                'display' => true,
                'text' => Yii::t('app', 'Win %'),
              ],
              'type' => 'linear',
            ],
          ],
        ],
      ],
    ],
  ]) . "\n" ?>
</div>
