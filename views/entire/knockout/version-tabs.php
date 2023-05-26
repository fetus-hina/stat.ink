<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $version
 */

$label = fn (int $version, string $name): string => Html::encode(Yii::t('app', $name));
$data = [
  [
    'version' => 3,
    'label' => $label(3, 'Splatoon 3'),
    'link' => ['entire/knockout3'],
  ],
  [
    'version' => 2,
    'label' => $label(2, 'Splatoon 2'),
    'link' => ['entire/knockout2'],
  ],
  [
    'version' => 1,
    'label' => $label(1, 'Splatoon'),
    'link' => ['entire/knockout'],
  ],
];

echo Html::tag(
  'ul',
  implode(
    '',
    ArrayHelper::getColumn(
      $data,
      fn (array $item): string => ($version === $item['version'])
        ? Html::tag(
          'li',
          Html::tag('a', $item['label']),
          ['class' => 'active'],
        )
        : Html::tag(
          'li',
          Html::a($item['label'], $item['link']),
        ),
    ),
  ),
  ['class' => 'mb-3 nav nav-tabs'],
);
