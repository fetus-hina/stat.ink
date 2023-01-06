<?php

declare(strict_types=1);

use app\models\Weapon3;
use app\models\XMatchingGroup3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Weapon3|null $weapon
 * @var XMatchingGroup3|null $group
 */

if (!$group) {
  echo Html::tag('td', '?');
  return;
}

$className = sprintf('x-matching-group-%d', $group->id);

$this->registerCss(
  vsprintf('.%s{%s}', [
    $className,
    Html::cssStyleFromArray([
      'background-color' => "#{$group->color}",
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
      'nobr',
      'text-center',
      $className,
    ],
    'title' => Yii::t('app-xmatch3', $group->name),
  ],
);
