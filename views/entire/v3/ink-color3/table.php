<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\StatInkColor3;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatInkColor3[] $models
 * @var View $this
 */

$provider = Yii::createObject([
  'class' => ArrayDataProvider::class,
  'allModels' => $models,
  'pagination' => false,
  'sort' => false,
]);

echo Html::tag(
  'div',
  GridView::widget([
    'dataProvider' => $provider,
    'emptyCell' => '',
    'emptyText' => '',
    'layout' => '{items}',
    'tableOptions' => ['class' => 'table table-striped table-bordered mb-0'],
    'columns' => [
      require __DIR__ . '/table/color1.php',
      require __DIR__ . '/table/color2.php',
      require __DIR__ . '/table/significance.php',
      require __DIR__ . '/table/winpct.php',
      require __DIR__ . '/table/samples.php',
    ],
  ]),
  ['class' => 'table-responsive'],
) . "\n";

echo Html::tag(
  'p',
  Html::encode('*: p < 0.05 / **: p < 0.01'),
  ['class' => ['mb-0', 'small', 'text-muted', 'text-right']],
) . "\n";

echo Html::tag(
  'p',
  Yii::t('app', 'Idea: {source}', [
    'source' => Html::a(
      vsprintf('%s %s', [
        Icon::twitter(),
        Html::encode('splatoon_stat'),
      ]),
      'https://twitter.com/splatoon_stat/status/1613143637231276032',
      [
        'target' => '_blank',
        'rel' => 'noopener noreferrer',
      ],
    ),
  ]),
  ['class' => 'mb-3 text-right'],
) . "\n";
