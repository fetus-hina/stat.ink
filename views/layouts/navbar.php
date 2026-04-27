<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\PaintballAsset;
use statink\yii2\ipBadge\IpBadgeWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

PaintballAsset::register($this);

?>
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="container">
      <div class="navbar-header">
        <?= Html::tag(
          'button',
          implode('', [
            Html::tag('span', 'Toggle navigation', ['class' => 'sr-only']),
            Html::tag('span', '', ['class' => 'icon-bar']),
            Html::tag('span', '', ['class' => 'icon-bar']),
            Html::tag('span', '', ['class' => 'icon-bar']),
          ]),
          [
            'type' => 'button',
            'class' => 'navbar-toggle collapsed',
            'data' => [
              'toggle' => 'collapse',
              'target' => '#bs-example-navbar-collapse-1',
            ],
            'aria-expanded' => "false",
          ]
        ) . "\n" ?>
        <?= Html::a(Html::encode(Yii::$app->name), '/', [
          'class' => 'navbar-brand paintball',
          'style' => [
            'font-size' => '24px',
          ],
          'itemprop' => 'name',
        ]) . "\n" ?>
        <span class="navbar-brand">
          <?= IpBadgeWidget::widget() . "\n" ?>
        </span>
      </div>
      <div itemscope itemtype="http://schema.org/SiteNavigationElement" class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <?= $this->render('/layouts/navbar/user') . "\n" ?>
          </li>
          <li class="dropdown">
            <?= $this->render('/layouts/navbar/language') . "\n" ?>
          </li>
          <li class="dropdown">
            <?= $this->render('/layouts/navbar/timezone') . "\n" ?>
          </li>
          <li class="dropdown">
            <?= $this->render('/layouts/navbar/link') . "\n" ?>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
