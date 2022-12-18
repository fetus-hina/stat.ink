<?php

declare(strict_types=1);

use app\assets\EntireKnockoutAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use statink\yii2\stages\spl1\Spl1Stage;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 */

$this->context->layout = 'main';

$title = Yii::t('app', 'Knockout Ratio');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

TableResponsiveForceAsset::register($this);
EntireKnockoutAsset::register($this);

$this->registerCss(Html::renderCss([
  'table' => [
    'min-width' => sprintf('%dpx', 220 * (count($rules) + 1)),
  ],
  'th,td' => [
    'width' => sprintf('%.f%%', 100 / (count($rules) + 1)),
  ],
]));

$ruleTotal = function (string $ruleKey) use ($data): ?array {
  $b = 0;
  $k = 0;
  foreach ($data as $map) {
    if ($tmp = $map->rules->{$ruleKey}) {
      $b += (int)$tmp->battles;
      $k += (int)$tmp->knockouts;
    }
  }
  if ($b < 1) {
    return null;
  }

  return [
    'battle' => $b,
    'ko' => $k,
  ];
}
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p><?= Html::encode(Yii::t('app', 'Excluded: Private Battles')) ?></p>
  <aside class="mb-3">
    <nav>
      <ul class="nav nav-tabs">
        <li><a href="/entire/knockout2">Splatoon 2</a></li>
        <li class="active"><a>Splatoon</a></li>
      </ul>
    </nav>
  </aside>

  <div class="table-responsive table-responsive-force">
    <table class="table table-condensed graph-container">
      <thead>
        <tr>
          <th></th>
<?php foreach ($rules as $ruleKey => $ruleName) { ?>
          <th><?= Html::encode($ruleName) ?></th>
<?php } ?>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th><?= $this->render('_knockout_legends') ?></th>
<?php foreach ($rules as $ruleKey => $ruleName) { ?>
          <td><?php
            if ($json = $ruleTotal($ruleKey)) {
              echo Html::tag('div', '', [
                'class' => 'pie-flot-container',
                'data' => [
                  'json' => Json::encode($json),
                ],
              ]);
            }
          ?></td>
<?php } ?>
        </tr>
<?php foreach ($data as $_) { ?>
<?php $map = $_->map ?>
        <tr>
          <th scope="row"><?php
            echo Html::encode($map->name) . '<br>';
            echo Spl1Stage::img('daytime', $map->key, ['class' => 'map-image']);
          ?></th>
<?php foreach ($rules as $ruleKey => $ruleName) { ?>
          <td><?php
            $tmp = $_->rules->{$ruleKey};
            if ($tmp && $tmp->battles > 0) {
              echo Html::tag('div', '', [
                'class' => 'pie-flot-container',
                'data' => [
                  'json' => Json::encode([
                    'battle' => (int)$tmp->battles,
                    'ko' => (int)$tmp->knockouts,
                  ]),
                ],
              ]);
            }
          ?></td>
<?php } ?>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
</div>
