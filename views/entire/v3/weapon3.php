<?php

declare(strict_types=1);

use app\actions\entire\v3\Weapon3Action;
use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\RatioAsset;
use app\assets\ShadowAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\SplatoonVersion3;
use app\models\Weapon3;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @phpstan-import-type DataType from Weapon3Action
 * @var DataType $data
 * @var Lobby3 $lobby
 * @var Rule3 $rule
 * @var Season3|null $season
 * @var SplatoonVersion3[] $versions
 * @var SplatoonVersion3|null $version
 * @var View $this
 * @var Weapon3 $weapon
 * @var Weapon3[] $weapons
 * @var array<int, Lobby3> $lobbies
 * @var array<int, Rule3> $rules
 * @var array<int, Season3> $seasons
 * @var callable(Lobby3): string $lobbyUrl
 * @var callable(Rule3): string $ruleUrl
 * @var callable(Season3): string $seasonUrl
 * @var callable(SplatoonVersion3): string $versionUrl
 * @var callable(Weapon3): string $weaponUrl
 */

$title = Yii::t('app-weapon3', $weapon->name);
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

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
ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
ColorSchemeAsset::register($this);
RatioAsset::register($this);
ShadowAsset::register($this);

echo $this->render('weapon3/chart-runner');

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mb-3">
    <?= Html::a(
      implode(' ', [
        Icon::back(),
        Html::encode(Yii::t('app', 'Back')),
      ]),
      ['entire/weapons3',
        'lobby' => $lobby->key,
        'rule' => $rule->key,
        'season' => $season?->id,
        'version' => $version?->tag,
      ],
      ['class' => 'btn btn-default'],
    ) . "\n" ?>
  </div>

  <div class="mb-3">
    <?= $this->render('includes/weapon-selector', compact('weapon', 'weapons', 'weaponUrl')) . "\n" ?>
    <div class="mb-1">
      <?= $this->render(
        'includes/season-selector',
        compact('season', 'seasonUrl', 'seasons', 'version', 'versionUrl', 'versions'),
      ) . "\n" ?>
    </div>
    <?= $this->render('weapon3/lobby-tabs', compact('lobby', 'lobbies', 'lobbyUrl')) . "\n" ?>
    <?= $this->render('weapon3/rule-tabs', compact('rule', 'rules', 'ruleUrl')) . "\n" ?>
  </div>

  <div class="mb-3">
    <p class="mb-1">
      <?= Html::encode(
        Yii::t('app', 'Aggregated: {rules}', [
          'rules' => Yii::t('app', '7 players for each battle (excluded the battle uploader)'),
        ]),
      ) . "\n" ?>
    </p>
    <p class="mb-1">
      <?= Html::encode(
        vsprintf('%s: %s', [
          Yii::t('app', 'Samples'),
          Yii::$app->formatter->asInteger(
            array_sum(
              array_map(
                fn (Model $v): int => $v->battles,
                $data['kill'],
              ),
            ),
          ),
        ]),
      ) . "\n" ?>
    </p>
  </div>
<?php
  if ($disableCache || $this->beginCache($cacheId, ['duration' => 48 * 3600])) {
    echo Html::tag(
      'div',
      implode('', [
        $this->render('weapon3/columns/kill', ['data' => $data['kill']]),
        $this->render('weapon3/columns/death', ['data' => $data['death']]),
        $this->render('weapon3/columns/assist', ['data' => $data['assist']]),
        $this->render('weapon3/columns/ka', ['data' => $data['ka']]),
        $this->render('weapon3/columns/special', ['data' => $data['special']]),
        $this->render('weapon3/columns/inked', ['data' => $data['inked']]),
      ]),
      ['class' => 'row'],
    );
    echo Html::tag(
      'div',
      implode('', [
        Html::tag(
          'p',
          Yii::t('app', 'Error bars: 95% confidence interval (estimated) & 99% confidence interval (estimated)'),
          ['class' => 'text-right small mb-1'],
        ),
      ]),
      ['class' => 'mb-3'],
    );

    if (!$disableCache) {
      $this->endCache();
    }
  }
?>
</div>
