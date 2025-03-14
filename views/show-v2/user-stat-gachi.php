<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
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
 * @var Rule2 $rule
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', "{name}'s Battle Stats ({rule})", [
    'name' => $user->name,
    'rule' => Yii::t('app-rule2', $rule->name),
]);
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
        <li role="presentation"><?= Html::a(
          Html::encode(Yii::t('app-rule2', 'Turf War')),
          ['show-v2/user-stat-nawabari', 'screen_name' => $user->screen_name]
        ) ?></li>
<?php $_rules = ArrayHelper::map(
  Rule2::find()->where(['not', ['key' => 'nawabari']])->asArray()->all(),
  'key',
  function (array $row): string {
    return Yii::t('app-rule2', $row['name']);
  }
) ?>
<?php asort($_rules) ?>
<?php foreach ($_rules as $_k => $_n) { ?>
        <?= Html::tag(
          'li',
          Html::a(
            Html::encode($_n),
            ['show-v2/user-stat-gachi', 'screen_name' => $user->screen_name, 'rule' => $_k]
          ),
          [
            'role' => 'presentation',
            'class' => $_k === $rule->key ? 'active' : '',
          ]
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
          'href' => '#stages',
        ]) . "\n" ?>
        <?= Html::encode(Yii::t('app', 'Stages')) . "\n" ?>
      </h2>
<?php foreach (Map2::getSortedMap() as $key => $name): ?>
      <?= Html::beginTag('div', ['class' => 'stage-inked', 'id' => 'stage-' . $key]) . "\n" ?>
        <h3>
          <?= Html::tag('a', Html::tag('span', '', ['class' => 'fas fa-link']), [
            'href' => '#stage-' . $key,
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
      'rank' => [
        'rank' => Yii::t('app', 'Rank'),
        'xpower' => Yii::t('app', 'X Power'),
      ],
    ]) . "\n" ?>
  </script>
  <script type="application/json" id="json-battles" data-has-rank="true"><?php
$_query = Battle2::find()
  ->innerJoinWith('rule', true)
  ->joinWith('lobby', false)
  ->with(['weapon', 'map', 'version', 'agent'])
  ->andWhere([
    '{{battle2}}.[[user_id]]' => $user->id,
    '{{battle2}}.[[rule_id]]' => $rule->id,
  ])
  ->andWhere(['or',
    ['{{battle2}}.[[lobby_id]]' => null],
    ['<>', '{{lobby2}}.[[key]]', 'private'],
  ])
  ->orderBy(['id' => SORT_ASC]);
echo "\n[\n";
$rankMap = [
    'c-'    => 0,
    'c'     => 1,
    'c+'    => 2,
    'b-'    => 3,
    'b'     => 4,
    'b+'    => 5,
    'a-'    => 6,
    'a'     => 7,
    'a+'    => 8,
    's'     => 9,
    's+'    => 10,
    'x'     => 20,
];
foreach ($_query->batch(200) as $_i => $_rows) {
  if ($_i > 0) {
    echo ",\n";
  }
  echo trim(
    Json::encode(array_map(
      function ($battle) use ($rankMap) {
        $rank = null;
        if ($battle->rank) {
          $rank = $rankMap[$battle->rank->key] ?? null;
          if ($rank !== null && $battle->rank->key === 's+') {
            $rank += (int)$battle->rank_exp;
          }
        }
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
          'r' => $rank,
          'x' => (($battle->rank->key ?? null) === 'x')
            ? ($battle->x_power ?? $battle->x_power_after ?? null)
            : null,
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
