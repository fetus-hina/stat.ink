<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\assets\UserStat2NawabariAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo2;
use app\models\Battle2;
use app\models\Map2;
use app\models\Rule2;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', "{name}'s Battle Stats (Turf War)", ['name' => $user->name]);
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

UserStat2NawabariAsset::register($this);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= SnsWidget::widget() . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
      <ul class="nav nav-tabs">
        <li role="presentation" class="active">
          <a><?= Html::encode(Yii::t('app-rule2', 'Turf War')) ?></a>
        </li>
<?php $_rules = ArrayHelper::map(
  Rule2::find()->where(['not', ['key' => 'nawabari']])->asArray()->all(),
  'key',
  function (array $row): string {
    return Yii::t('app-rule2', $row['name']);
  }
) ?>
<?php asort($_rules) ?>
<?php foreach ($_rules as $_k => $_n) { ?>
        <li role="presentation"><?= Html::a(
          Html::encode($_n),
          ['show-v2/user-stat-gachi', 'screen_name' => $user->screen_name, 'rule' => $_k]
        ) ?></li>
<?php } ?>
      </ul>
      <h2 id="wp">
        <?= Html::tag('a', Html::tag('span', '', ['class' => 'fas fa-link']), [
          'href' => '#wp',
        ]) . "\n" ?>
        <?= Html::encode(Yii::t('app', 'Winning Percentage')) . "\n" ?>
      </h2>
      <p>
        <?= Html::encode(Yii::t('app', 'Excluded: Private Battles')) . "\n" ?>
      </p>
      <div id="stat-wp-legend"></div>
      <div class="graph stat-wp"></div>
      <div class="graph stat-wp" data-limit="200"></div>

      <h2 id="stats">
        <?= Html::tag('a', Html::tag('span', '', ['class' => 'fas fa-link']), [
          'href' => '#stats',
        ]) . "\n" ?>
        <?= Html::encode(Yii::t('app', 'Stats')) . "\n" ?>
      </h2>
      <p>
        <?= Html::encode(Yii::t('app', 'Excluded: Private Battles')) . "\n" ?>
      </p>
      <div id="stat-stats-legend"></div>
      <div class="graph stat-stats"></div>
      <div class="graph stat-stats" data-limit="200"></div>

      <h2 id="inked">
        <?= Html::tag('a', Html::tag('span', '', ['class' => 'fas fa-link']), [
          'href' => '#inked',
        ]) . "\n" ?>
        <?= Html::encode(Yii::t('app', 'Turf Inked')) . "\n" ?>
      </h2>
      <div class="stage-inked">
        <div class="graph stat-inked"></div>
      </div>
<?php foreach (Map2::getSortedMap() as $key => $name): ?>
      <?= Html::beginTag('div', ['class' => 'stage-inked', 'id' => 'inked-' . $key]) . "\n" ?>
        <h3>
          <?= Html::tag('a', Html::tag('span', '', ['class' => 'fas fa-link']), [
            'href' => '#inked-' . $key,
          ]) . "\n" ?>
          <?= Html::encode($name) . "\n" ?>
        </h3>
        <?= Html::tag('div', '', [
          'class' => 'graph stat-inked',
          'data' => [
            'filter' => $key,
          ],
        ]) . "\n" ?>
      </div>
<?php endforeach; ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= UserMiniInfo2::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
  <script type="application/json" id="json-strings">
    <?= Json::encode([
      'wp' => [
        'entire' => Yii::t('app', 'Winning Percentage'),
        'last20' => Yii::t('app', 'Win % ({0} Battles)', [20]),
        'last50' => Yii::t('app', 'Win % ({0} Battles)', [50]),
      ],
      'stats' => [
        'killRatio' => Yii::t('app', 'Kill Ratio'),
        'avgKill' => Yii::t('app', 'Avg Kills'),
        'avgDeath' => Yii::t('app', 'Avg Deaths'),
        'avgSpecial' => Yii::t('app', 'Avg Specials'),

        'KR' => Yii::t('app', 'KR'),
      ],
      'inked' => [
        'turfInked' => Yii::t('app', 'Turf Inked'),
      ],
    ]) . "\n" ?>
  </script>
  <script type="application/json" id="json-battles"><?php
$_query = Battle2::find()
  ->innerJoinWith('rule', true)
  ->joinWith('lobby', false)
  ->with(['weapon', 'map', 'version', 'agent'])
  ->andWhere([
    '{{battle2}}.[[user_id]]' => $user->id,
    '{{rule2}}.[[key]]' => 'nawabari',
  ])
  ->andWhere(['or',
    ['{{battle2}}.[[lobby_id]]' => null],
    ['<>', '{{lobby2}}.[[key]]', 'private'],
  ])
  ->orderBy(['id' => SORT_ASC]);
echo "\n[\n";
foreach ($_query->batch(200) as $_i => $_rows) {
  if ($_i > 0) {
    echo ",\n";
  }
  echo trim(
    Json::encode(array_map(
      function ($battle) {
        return [
          'win' => $battle->is_win,
          'ink' => $battle->inked,
          'k' => $battle->kill,
          'd' => $battle->death,
          'as' => ($battle->kill !== null && $battle->kill_or_assist !== null)
            ? ($battle->kill_or_assist - $battle->kill)
            : null,
          'sp' => $battle->special,
          'sta' => $battle->map->key ?? null,
          'wea' => $battle->weapon->key ?? null,
          'ver' => $battle->version->tag ?? null,
        ];
      },
      $_rows
    )),
    '[]'
  );
}
echo "]\n";
  ?></script>
</div>
