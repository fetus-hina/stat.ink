<?php

declare(strict_types=1);

use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatXPowerDistribAbstract3;
use app\models\StatXPowerDistribHistogram3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var Season3 $season
 * @var StatXPowerDistribAbstract3|null $abstract
 * @var View $this
 */

$assetRevision = ArrayHelper::getValue(Yii::$app->params, 'assetRevision');

$histogramData = Yii::$app->cache->getOrSet(
  [
    __FILE__,
    __LINE__,
    2, // version
    $season->id,
    $rule->id,
    $abstract?->attributes,
  ],
  fn (): array => StatXPowerDistribHistogram3::find()
    ->andWhere([
      'season_id' => $season->id,
      'rule_id' => $rule->id,
    ])
    ->orderBy(['class_value' => SORT_ASC])
    ->all(),
  7200,
);

$histogramDataId = array_map(
  fn (StatXPowerDistribHistogram3 $model): array => [$model->class_value, $model->users],
  $histogramData,
);

RatioAsset::register($this);
JqueryEasyChartjsAsset::register($this);

$this->registerJs(vsprintf('$(%s).easyChartJs();', [
  Json::encode('.xpower-distrib-chart'),
]));

?>
<div class="mb-4">
  <?= $this->render('../includes/rule-header', [
    'rule' => $rule,
    'id' => $rule->key,
  ]) . "\n" ?>
  <?= Yii::$app->cache->getOrSet(
    [
      __FILE__,
      __LINE__,
      Yii::$app->language,
      @hash_file('sha256', __DIR__ . '/rule/abstract.php'),
      $abstract?->attributes,
    ],
    fn (): string => $this->render('rule/abstract', ['model' => $abstract]),
    86400,
  ) . "\n" ?>
  <?= Yii::$app->cache->getOrSet(
    [
      __FILE__,
      __LINE__,
      $assetRevision,
      Yii::$app->language,
      @hash_file('sha256', __DIR__ . '/rule/histogram.php'),
      $histogramDataId,
    ],
    fn (): string =>  $this->render(
      'rule/histogram',
      [
        'abstract' => $abstract,
        'data' => $histogramData,
      ],
    ),
    86400,
  ) . "\n" ?>
</div>
