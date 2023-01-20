<?php

declare(strict_types=1);

use app\assets\GameVersionIconAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$versionAsset = GameVersionIconAsset::register($this);

$icon = fn (int $version): string => Html::img(
  $versionAsset->getIconUrl($version),
  [
    'class' => 'basic-icon',
    'draggable' => 'false',
    'style' => [
      '--icon-height' => '1em',
    ],
  ],
);

?>
<aside class="mb-3">
  <nav><?= Html::tag(
    'ul',
    implode('', [
      Html::tag(
        'li',
        Html::tag(
          'a',
          implode(' ', [
            $icon(3),
            Html::encode(Yii::t('app', 'Splatoon 3')),
          ]),
        ),
        ['class' => 'active'],
      ),
      Html::tag(
        'li',
        Html::a(
          implode(' ', [
            $icon(2),
            Html::encode(Yii::t('app', 'Splatoon 2')),
          ]),
          ['entire/weapons2'],
        ),
      ),
      Html::tag(
        'li',
        Html::a(
          implode(' ', [
            $icon(1),
            Html::encode(Yii::t('app', 'Splatoon')),
          ]),
          ['entire/weapons'],
        ),
      ),
    ]),
    ['class' => 'nav nav-tabs'],
  ) ?></nav>
</aside>
