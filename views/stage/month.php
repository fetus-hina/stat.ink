<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var DateTimeInterface $month
 * @var View $this
 * @var object[] $rules
 * @var string|array $nextUrl
 * @var string|array $prevUrl
 */

$formatter = Yii::$app->formatter;

$date = $month->format('Y-m');
$title = sprintf(
  '%s - %s',
  Yii::t('app', 'Stages'),
  $month->format('Y-m')
);
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$nextPrev = [];
if ($prevUrl) {
  $nextPrev[] = Html::tag(
    'div',
    Html::a(
      implode(' ', [
        Icon::prevPage(),
        Html::encode(Yii::t('app', 'Prev. Month')),
      ]),
      $prevUrl,
      ['class' => 'btn btn-default']
    ),
    ['class' => 'col-xs-6']
  );
}
if ($nextUrl) {
  $nextPrev[] = Html::tag(
    'div',
    Html::a(
      implode('', [
        Html::encode(Yii::t('app', 'Next Month')),
        Icon::nextPage(),
      ]),
      $nextUrl,
      ['class' => 'btn btn-default']
    ),
    ['class' => 'col-xs-6 pull-right text-right']
  );
}

$this->registerCss('tr.max>td{font-weight:bold}');
?> 
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

<?php if ($nextPrev) { ?>
  <div class="row" style="margin-bottom:15px">
    <?= implode('', $nextPrev) . "\n" ?>
  </div>
<?php } ?>

  <div class="row">
<?php foreach ($rules as $_rule) { ?>
<?php $rule = $_rule->rule ?>
<?php $maxCount = -1 ?>
    <div class="col-xs-12 col-sm-6 col-lg-3" id="<?= Html::encode($rule->key) ?>">
      <table class="table table-striped">
        <thead>
          <tr>
            <th><?= Html::encode(Yii::t('app-rule', $rule->name)) ?></th>
            <th><?= Html::encode(Yii::t('app', 'Times')) ?></th>
          </tr>
        </thead>
        <tbody>
<?php $total = 0 ?>
<?php foreach ($_rule->maps as $_map) { ?>
<?php $map = $_map->map ?>
<?php $count = $_map->count ?>
<?php $total += $count ?>
<?php $maxCount = max($maxCount, $count) ?>
          <?= Html::beginTag('tr', ['class' => ($count == $maxCount ? 'max' : '')]) . "\n" ?>
            <td>
              <?= Html::a(
                Html::encode(Yii::t('app-map', $map->name)),
                ['stage/map', 'map' => $map->key, '#' => $rule->key]
              ) . "\n" ?>
            </td>
            <td class="text-right">
              <?= Html::encode($formatter->asInteger($count)) . "\n" ?>
            </td>
          </tr>
<?php } ?>
          <tr>
            <td><?= Html::encode(Yii::t('app', 'Total')) ?></td>
            <td class="text-right">
              <?= Html::encode($formatter->asInteger($total)) . "\n" ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
<?php } ?>
  </div>
</div>
