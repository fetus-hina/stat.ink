<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Weapon3;
use app\models\XMatchingGroup3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Weapon3 $weapon
 * @var XMatchingGroup3|null $group
 */

if (!$group) {
  echo Html::tag('td', '', ['data-sort-value' => 0x7fffffff]);
  return;
}

$this->registerCss(
  vsprintf('.x-matching-%d{%s}', [
    $group->id,
    Html::cssStyleFromArray([
      'background-color' => sprintf('#%s', $group->color),
      'color' => '#333',
    ]),
  ]),
);

echo Html::tag(
  'td',
  Html::encode(Yii::t('app-xmatch3', $group->short_name)),
  [
    'class' => [
      'auto-tooltip',
      'text-center',
      "x-matching-{$group->id}",
    ],
    'data' => [
      'sort-value' => $group->rank,
    ],
    'title' => Yii::t('app-xmatch3', $group->name),
  ],
);
