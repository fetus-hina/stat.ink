<?php

declare(strict_types=1);

use app\assets\EntireXpowerDistrib3HistogramAsset;
use app\assets\GameModeIconsAsset;
use app\assets\RatioAsset;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatXPowerDistrib3;
use app\models\StatXPowerDistribAbstract3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var Season3 $season
 * @var StatXPowerDistribAbstract3|null $abstract
 * @var View $this
 */

$assetRevision = ArrayHelper::getValue(Yii::$app->params, 'assetRevision');

$histogramData = Yii::$app->cache->getOrSet(
  [__FILE__, __LINE__, $season->id, $rule->id, $abstract->attributes],
  fn (): array => StatXPowerDistrib3::find()
    ->andWhere([
      'season_id' => $season->id,
      'rule_id' => $rule->id,
    ])
    ->orderBy(['x_power' => SORT_ASC])
    ->all(),
  7200,
);

$histogramDataId = array_map(
  fn (StatXPowerDistrib3 $model): array => [$model->x_power, $model->users],
  $histogramData,
);

EntireXpowerDistrib3HistogramAsset::register($this);
RatioAsset::register($this);

?>
<div class="mb-3">
  <?= Yii::$app->cache->getOrSet(
    [__FILE__, __LINE__, $assetRevision, Yii::$app->language, $rule->id, $rule->name],
    fn (): string => $this->render('rule/heading', ['rule' => $rule]),
    86400,
  ) . "\n" ?>
  <?= Yii::$app->cache->getOrSet(
    [__FILE__, __LINE__, Yii::$app->language, $abstract->attributes],
    fn (): string => $this->render('rule/abstract', ['model' => $abstract]),
    86400,
  ) . "\n" ?>
  <?= Yii::$app->cache->getOrSet(
    [__FILE__, __LINE__, $assetRevision, Yii::$app->language, $histogramDataId],
    fn (): string =>  $this->render('rule/histogram', ['data' => $histogramData]),
    86400,
  ) . "\n" ?>
</div>
