<?php

declare(strict_types=1);

use yii\base\Model;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Model[] $data
 * @var View $this
 * @var string $xLabel
 * @var string|string[]|callable $xGet
 */

echo Html::tag(
  'div',
  Html::tag(
    'div',
    Html::tag(
      'div',
      implode('', [
        $this->render('column/heading', compact('xLabel')),
        $this->render('column/chart', compact('data', 'xGet', 'xLabel')),
      ]),
      ['class' => 'panel-body'],
    ),
    ['class' => 'panel panel-default shadow-sm'],
  ),
  ['class' => 'col-xs-12 col-md-6 mb-3'],
);
