<?php

declare(strict_types=1);

use Base32\Base32;
use app\assets\EntireAgentAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\helpers\Json;

$title = sprintf(
  '%s - %s',
  Yii::t('app', 'Battles and Users'),
  $name
);
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

EntireAgentAsset::register($this);

$this->registerCss('#graph{height:300px}');
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-6">
      <p>
        <?= Html::a(
          implode('', [
            Icon::back(),
            Html::encode(Yii::t('app', 'Back')),
          ]),
          ['entire/users'],
          ['class' => 'btn btn-default']
        ) . "\n" ?>
      </p>
    </div>
<?php if ($combineds): ?>
    <div class="col-xs-6">
      <p class="text-right">
<?php foreach ($combineds as $_combined): ?>
        <?= Html::a(
          implode('', [
            Html::encode(
              sprintf('%s %s', $_combined['name'], Yii::t('app', '(combined)'))
            ),
            Icon::subPage(),
          ]),
          ['entire/combined-agent',
            'b32name' => strtolower(rtrim(Base32::encode($_combined['name']), '=')),
          ],
          ['class' => 'btn btn-default']
        ) . "\n" ?>
<?php endforeach ?>
      </p>
    </div>
<?php endif ?>
  </div>

  <?= Html::tag('div', '', [
    'data' => [
      'data' => Json::encode($posts),
      'label-battle' => Yii::t('app', 'Battles'),
      'label-user' => Yii::t('app', 'Users'),
    ],
    'id' => 'graph',
  ]) . "\n" ?>
</div>
