<?php
use app\assets\PaintballAsset;
use app\components\widgets\IpVersionBadgeWidget;
use yii\helpers\Html;

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
        <span class="navbar-brand ip-via-badge">
<?php $this->registerCss('.ip-via-badge{position:relative;top:-3px}') ?>
          <?= IpVersionBadgeWidget::widget() . "\n" ?>
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
<?php if (!Yii::$app->user->isGuest) { ?>
        <ul class="nav navbar-nav navbar-right">
          <li style="margin-right:1ex">
            <?= Html::tag(
              'button',
              implode('', [
                Html::tag('span', '', ['class' => 'fa fa-fw fa-pencil-square-o']),
                Html::encode(Yii::t('app', 'Splatoon 2')),
              ]),
              [
                'id' => 'battle-input2-btn',
                'class' => 'btn btn-primary navbar-btn',
                'title' => Yii::t('app', 'New battle'),
                'disabled' => true,
              ]
            ) . "\n" ?>
          </li>
          <li>
            <?= Html::tag(
              'button',
              implode('', [
                Html::tag('span', '', ['class' => 'fa fa-fw fa-pencil-square-o']),
                Html::encode(Yii::t('app', 'Splatoon')),
              ]),
              [
                'id' => 'battle-input-btn',
                'class' => 'btn btn-primary navbar-btn',
                'title' => Yii::t('app', 'New battle'),
                'disabled' => true,
              ]
            ) . "\n" ?>
          </li>
        </ul>
<?php } ?>
      </div>
    </div>
  </div>
</nav>
