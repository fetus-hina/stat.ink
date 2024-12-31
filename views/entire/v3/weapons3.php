<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\BattleListGroupHeaderAsset;
use app\assets\BattleSummaryDialogAsset;
use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\RatioAsset;
use app\assets\ShadowAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\SplatoonVersion3;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use app\models\StatWeapon3XUsagePerVersion;
use app\models\StatWeapon3XUsageRange;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var Rule3 $rule
 * @var Season3|null $season
 * @var SplatoonVersion3[] $versions
 * @var SplatoonVersion3|null $version
 * @var StatWeapon3Usage[]|StatWeapon3UsagePerVersion[]|StatWeapon3XUsage[]|StatWeapon3XUsagePerVersion[] $data
 * @var StatWeapon3XUsageRange $xRange
 * @var StatWeapon3XUsageRange[] $xRanges
 * @var View $this
 * @var array<int, Lobby3> $lobbies
 * @var array<int, Rule3> $rules
 * @var array<int, Season3> $seasons
 * @var callable(Lobby3): string $lobbyUrl
 * @var callable(Rule3): string $ruleUrl
 * @var callable(Season3): string $seasonUrl
 * @var callable(SplatoonVersion3): string $versionUrl
 * @var callable(StatWeapon3XUsageRange|null): string $xRangeUrl
 */

$title = Yii::t('app', 'Weapons');
$this->title = $title . ' | ' . Yii::$app->name;

OgpHelper::default($this, title: $title);

$disableCache = YII_ENV_DEV;
$cacheId = hash_hmac(
  'sha256',
  Json::encode($data),
  vsprintf('%s?%s', [
    urlencode(__FILE__),
    http_build_query([
      'app_revision' => (string)ArrayHelper::getValue(Yii::$app->params, 'gitRevision.longHash'),
      'app_version' => (string)Yii::$app->version,
      'asset_revision' => (string)ArrayHelper::getValue(Yii::$app->params, 'assetRevision'),
      'cache_version' => 1,
      'dev_cache' => YII_ENV_DEV ? (string)@filemtime(__FILE__) : '',
      'lang' => Yii::$app->language,
    ]),
  ]),
);

// Register asset bundles to use fragment cache
BattleListGroupHeaderAsset::register($this);
BattleSummaryDialogAsset::register($this);
ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
ColorSchemeAsset::register($this);
RatioAsset::register($this);
ShadowAsset::register($this);
SortableTableAsset::register($this);
TableResponsiveForceAsset::register($this);

$this->render('weapons3/charts/includes/chart-runner', []);

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= $this->render('weapons3/version-tabs') . "\n" ?>
  <div class="mb-3">
    <div class="mb-1">
      <?= $this->render(
        'includes/season-selector',
        compact('season', 'seasonUrl', 'seasons', 'version', 'versionUrl', 'versions'),
      ) . "\n" ?>
    </div>
    <?= $this->render('weapons3/lobby-tabs', compact('lobby', 'lobbies', 'lobbyUrl')) . "\n" ?>
    <?= $this->render('weapons3/rule-tabs', compact('rule', 'rules', 'ruleUrl')) . "\n" ?>
    <?= $this->render('weapons3/x-range-tabs', compact('xRange', 'xRanges', 'xRangeUrl')) . "\n" ?>
  </div>
<?php if ($xRange && $version) { ?>
  <div class="mb-3">
    <div class="alert alert-warning">
      <?= Yii::t(
        'app',
        'The filter specifying XP and version, aggregates data for the overall <code>x.y</code>, ignoring the <code>z</code> in version <code>x.y.z</code>.',
      ) . "\n" ?>
    </div>
  </div>
<?php } ?>
<?php
  if ($disableCache || $this->beginCache($cacheId, ['duration' => 48 * 3600])) {
    echo implode('', [
      $this->render('weapons3/summary', compact('data', 'rule')),
      $this->render('weapons3/table', compact('data')),
      $this->render('weapons3/charts', compact('data')),
    ]);
    if (!$disableCache) {
      $this->endCache();
    }
  }
?>
</div>
