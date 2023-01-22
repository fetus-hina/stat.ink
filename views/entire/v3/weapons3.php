<?php

declare(strict_types=1);

use app\assets\BattleListGroupHeaderAsset;
use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\RatioAsset;
use app\assets\ShadowAsset;
use app\assets\Spl3WeaponAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\SplatoonVersion3;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
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
 * @var StatWeapon3Usage[]|StatWeapon3UsagePerVersion[] $data
 * @var View $this
 * @var array<int, Lobby3> $lobbies
 * @var array<int, Rule3> $rules
 * @var array<int, Season3> $seasons
 * @var callable(Lobby3): string $lobbyUrl
 * @var callable(Rule3): string $ruleUrl
 * @var callable(Season3): string $seasonUrl
 * @var callable(SplatoonVersion3): string $versionUrl
 */

$title = Yii::t('app', 'Weapons');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$disableCache = false;
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
ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
ColorSchemeAsset::register($this);
RatioAsset::register($this);
ShadowAsset::register($this);
SortableTableAsset::register($this);
Spl3WeaponAsset::register($this);
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
  </div>
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
