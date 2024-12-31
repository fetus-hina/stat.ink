<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Map;
use statink\yii2\bukiicons\Bukiicons;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Map $map
 * @var View $this
 * @var array<string, string> $maps
 * @var object[] $data
 */

$formatter = Yii::$app->formatter;
$now = (int)($_SERVER['REQUEST_TIME'] ?? time());

$title = sprintf(
  '%s - %s',
  Yii::t('app', 'Stages'),
  Yii::t('app-map', $map->name)
);

$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$this->registerCss('td.range{width:1em;text-align:center;padding-left:0;padding-right:0}');

$hasTrend = false;
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p class="form-inline">
    <select class="form-control" id="change-map" disabled>
      <?= implode('', array_map(
        function ($key, $value) use ($map) : string {
          return Html::tag(
            'option',
            Html::encode($value),
            [
              'value' => Url::to(['stage/map', 'map' => $key]),
              'selected' => $key === $map->key,
            ]
          );
        },
        array_keys($maps),
        array_values($maps)
      )) . "\n" ?>
    </select>
<?php $this->registerJs(<<<'JS'
(function($){
  "use strict";
  $(function(){
    $('#change-map')
     .change(function(){window.location.href=$(this).val()})
     .prop('disabled',!1)
  })
})(jQuery);
JS
) ?>
  </p>

  <p>
    <?= implode(' | ', array_map(
      function ($rule) : string {
        return Html::a(
          Html::encode(Yii::t('app-rule', $rule->rule->name)),
          '#' . $rule->rule->key
        );
      },
      $data
    )) . "\n" ?>
  </p>

  <div class="row">
<?php foreach ($data as $__rule): ?>
<?php $_rule = $__rule->rule ?>
<?php $_data = $__rule->history ?>
    <?= Html::beginTag('div', ['id' => $_rule->key, 'class' => 'col-xs-12 col-sm-6']) . "\n" ?>
      <h2>
        <?= Html::a(
          Html::encode(Yii::t('app-rule', $_rule->name)),
          ['stage/map-detail', 'rule' => $_rule->key, 'map' => $map->key]
        ) . "\n" ?>
      </h2>

<?php if ($__rule->trends): ?>
<?php $this->registerCss(<<<'CSS'
.trends ul{list-style-type:none;display:block;overflow:hidden;margin:0;padding:0}
.trends ul li{display:inline-block;width:20%;margin:0;padding:0}
.trends img{width:100%;height:auto}
CSS
) ?>
<?php $hasTrend = true ?>
      <h3><?= Html::Encode(Yii::t('app', 'Trends')) ?></h3>
      <div class="trends">
        <ul>
          <?= implode('', array_map(
            function ($trend) use ($formatter, $__rule) : string {
              return Html::tag('li', Bukiicons::icon($trend->weapon->key, [
                'class' => 'auto-tooltip',
                'alt' => Yii::t('app-weapon', $trend->weapon->name),
                'title' => sprintf(
                  '%s / %s',
                  Yii::t('app-weapon', $trend->weapon->name),
                  $formatter->asPercent(($trend->battles / $__rule->trendTotalBattles), 2)
                ),
              ]));
            },
            $__rule->trends
          )) . "\n" ?>
        </ul>
        <p class="text-right">
          <?= Html::a(
            Html::encode(Yii::t('app', 'Details')),
            ['stage/map-detail', 'rule' => $_rule->key, 'map' => $map->key, '#' => 'weapons']
          ) . "\n" ?>
        </p>
      </div>

<?php endif ?>
      <h3><?= Html::encode(Yii::t('app', 'History')) ?></h3>
      <table class="table table-striped">
        <thead>
          <tr>
            <th></th>
            <th colspan="3" class="text-center"><?= Html::encode(Yii::t('app', 'Period')) ?></th>
            <th class="text-center"><?= Html::encode(Yii::t('app', 'Interval')) ?></th>
          </tr>
        </thead>
        <tbody>
<?php foreach ($_data as $_h): ?>
          <tr>
            <?= Html::tag('td', Html::encode(
              (function (int $start, int $end) use ($now, $formatter) : string {
                if ($start > $now) {
                  return Yii::t('app', 'Scheduled');
                } elseif ($end > $now) {
                  return Yii::t('app', 'In session');
                } else {
                  return $formatter->asRelativeTime($end, $now);
                }
              })($_h->start, $_h->end)
            )) . "\n" ?>
            <td class="text-right"><?= Html::tag(
              'time',
              Html::encode($formatter->asDatetime($_h->start)),
              ['datetime' => gmdate(DateTime::ATOM, $_h->start)]
            ) ?></td>
            <td class="range">-</td>
            <td class="text-left"><?= Html::tag(
              'time',
              Html::encode($formatter->asDatetime($_h->end)),
              ['datetime' => gmdate(DateTime::ATOM, $_h->end)]
            ) . "\n" ?></td>
            <td class="text-left"><?php
              if ($_h->interval === null) {
                echo Html::tag('div', '-', ['class' => 'text-center']);
              } elseif ($_h->interval === 0) {
                echo Html::encode(Yii::t('app', 'Continue'));
              } else {
                echo Html::encode($formatter->asDuration($_h->interval));
              }
            ?></td>
          </tr>
<?php endforeach ?>
<?php if (count($_data) >= 5): ?>
          <tr>
            <td class="text-right" colspan="5">
              <?= Html::a(
                Html::encode(Yii::t('app', 'more...')),
                ['stage/map-detail', 'rule' => $_rule->key, 'map' => $map->key, '#' => 'history']
              ) . "\n" ?>
            </td>
          </tr>
<?php endif ?>
        </tbody>
      </table>
    </div>
<?php endforeach ?>
  </div>
<?php if ($hasTrend): ?>
  <p class="text-right">
    <?= Html::encode(Yii::t('app', "Weapons' icon were created by {0}.", ['Stylecase'])) . "\n" ?>
  </p>
<?php endif ?>
</div>
