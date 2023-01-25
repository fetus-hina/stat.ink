<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $xLabel
 */

echo Html::tag(
  'h2',
  Html::encode($xLabel),
  ['class' => 'mt-0 mb-2 h4 text-center'],
);
