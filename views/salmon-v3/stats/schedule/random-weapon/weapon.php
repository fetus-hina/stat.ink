<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $count
 * @var int $id
 * @var int $totalCount
 * @var string $key
 * @var string $name
 */

$panelSize = 84;
$panelPadding = 15;
$iconSize = $panelSize - $panelPadding * 2;

?>
<?= Html::beginTag('div', [
  'class' => [
    'd-inline-block',
    'mb-2',
    'me-2',
    'mr-2',
    'panel',
    'panel-default',
    'pull-left',
  ],
  'style' => [
    'width' => $panelSize . 'px',
    'height' => $panelSize . 'px',
  ],
]) . "\n" ?>
  <div class="panel-body" style="position:relative">
    <?= Html::tag(
      $count > 0 ? null : 'span', 
      Icon::s3Weapon($key, "{$iconSize}px"),
      [
        'style' => [
          'filter' => 'grayscale(1)',
          'opacity' => '0.25',
        ],
      ],
    ) . "\n" ?>
    <?= Html::tag(
      'div',
      Html::encode($count),
      [
        'class' => 'auto-tooltip badge',
        'style' => [
          'font-size' => '70%',
          'position' => 'absolute',
          'right' => "calc({$panelPadding}px / 2)",
          'top' => "calc({$panelPadding}px / 2)",
        ],
        'title' => $count > 0 && $totalCount > 0
          ? Yii::$app->formatter->asPercent($count / $totalCount, 2)
          : null,
      ],
    ) . "\n" ?>
  </div>
</div>
