<?php

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\StatSalmon3Salmometer;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, StatSalmon3Salmometer> $data
 */

$this->context->layout = 'main';

$title = vsprintf('%s - %s', [
  Yii::t('app-salmon3', 'Salmon Run'),
  Yii::t('app-salmon3', 'Salmometer'),
]);
$this->title = vsprintf('%s | %s', [
  $title,
  Yii::$app->name,
]);

OgpHelper::default($this, title: $this->title);

$totalSamples = array_sum(ArrayHelper::getColumn($data, 'jobs'));
$totalCleared = array_sum(ArrayHelper::getColumn($data, 'cleared'));

?>
<div class="container">
  <?= Html::tag(
    'h1',
    implode(' ', [
      Icon::s3Salmometer(),
      Html::encode(Yii::t('app-salmon3', 'Salmometer')),
    ]),
  ) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mt-0 mb-3">
    <p class="mt-0 mb-1">
      <?= Html::encode(
        Yii::t('app', 'Aggregated: {rules}', [
          'rules' => implode(', ', [
            Yii::t('app-salmon3', 'Normal Job'),
            Yii::t('app-salmon-title3', 'Eggsecutive VP'),
          ]),
        ]),
      ) . "\n" ?>
    </p>
    <p class="mt-0 mb-1">
      <?= Html::encode(
        Yii::t('app', 'Error bars: 95% confidence interval (estimated) & 99% confidence interval (estimated)'),
      ) . "\n" ?>
    </p>
  </div>

  <div class="row">
    <div class="col-12 col-xs-12 col-md-7 col-lg-8 mb-3">
      <?= $this->render('salmometer/chart', compact('data', 'totalSamples', 'totalCleared')) . "\n" ?>
    </div>
    <div class="col-12 col-xs-12 col-md-5 col-lg-4 mb-3">
      <?= $this->render('salmometer/table', compact('data', 'totalSamples', 'totalCleared')) . "\n" ?>
    </div>
  </div>
</div>
