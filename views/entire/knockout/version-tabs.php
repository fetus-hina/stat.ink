<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $version
 */

$label = fn (int $version, string $name): string => trim(
  implode(' ', [
    match ($version) {
      1 => Icon::splatoon1(),
      2 => Icon::splatoon2(),
      3 => Icon::splatoon3(),
      default => '',
    },
    Html::encode(Yii::t('app', $name)),
  ]),
);

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
