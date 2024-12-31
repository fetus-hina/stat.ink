<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\InlineListAsset;
use app\assets\UserStatGachiAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$this->context->layout = 'main';
$title = Yii::t('app', '{name}\'s Battle Stats (Ranked Battle)', [
  'name' => $user->name,
]);
$this->title = $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

UserStatGachiAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
      <h2 id="exp"><?= Html::encode(Yii::t('app', 'Rank')) ?></h2>
      <div style="margin-bottom:15px">
        <div class="row">
          <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
            <div class="user-label"><?= Html::encode(Yii::t('app', 'Current')) ?></div>
            <div class="user-number"><?= $userRankStat
              ? Html::encode(sprintf('%s %s', $userRankStat->rank, $userRankStat->rankExp))
              : Html::encode(Yii::t('app', 'N/A'))
            ?></div>
          </div>
        </div>
      </div>
      <p><?= Html::encode(
        Yii::t('app', 'Excluded: Private Battles and Squad Battles (when Rank S or S+)')
      ) ?></p>

<?php $this->registerJs(vsprintf('$(%s).rankHistory($(%s), $(%s), %s, %s);', [
  Json::encode('.stat-rank'),
  Json::encode('#stat-rank-legend'),
  Json::encode('#show-rank-moving-avg'),
  Json::encode($recentRank),
  Json::encode([
    'area' => sprintf('%s (%s)', Yii::t('app', 'Rank'), Yii::t('app-rule', 'Splat Zones')),
    'yagura' => sprintf('%s (%s)', Yii::t('app', 'Rank'), Yii::t('app-rule', 'Tower Control')),
    'hoko' => sprintf('%s (%s)', Yii::t('app', 'Rank'), Yii::t('app-rule', 'Rainmaker')),
    'movingAvg10' => Yii::t('app', 'Moving Avg. ({0} Battles)', [10]),
    'movingAvg50' => Yii::t('app', 'Moving Avg. ({0} Battles)', [50]),
  ]),
])) ?>
      <div id="stat-rank-legend"></div>
      <div class="graph stat-rank"></div>
      <div class="graph stat-rank" data-limit="200"></div>
      <div class="text-right"><?php
        echo Html::tag('label', implode(' ', [
          Html::tag('input', '', [
            'type' => 'checkbox',
            'id' => 'show-rank-moving-avg',
            'value' => '1',
            'checked' => true,
          ]),
          Html::encode(Yii::t('app', 'Show moving averages')),
        ]));
      ?></div>
      <hr>
      <h2 id="wp"><?= Html::encode(Yii::t('app', 'Winning Percentage')) ?></h2>
      <p><?= Html::encode(Yii::t('app', 'Excluded: Private Battles')) ?></p>
      <aside>
        <nav>
<?php InlineListAsset::register($this) ?>
          <ul class="inline-list"><?= implode('', array_map(
            function (string $key, string $name): string {
              return Html::tag('li', Html::a(
                Html::encode($name),
                '#wp-' . $key
              ));
            },
            array_keys($maps),
            array_values($maps)
          )) ?></ul>
        </nav>
      </aside>
      <script>
        /* window._maps = {{$maps|array_keys|json_encode}}; */
        /* window._wpData = {{$recentWP|json_encode}}; */
      </script>
      <div id="stat-wp-legend"></div>
      <div class="graph stat-wp"></div>
      <div class="graph stat-wp" data-limit="200"></div>
<?php foreach ($maps as $mapKey => $mapName) { ?>
      <?= Html::tag(
        'h3',
        implode('', [
          Html::tag('span', Html::encode(Yii::t('app', 'Winning Percentage') . ' - '), [
            'clas' => 'hidden-xs',
          ]),
          Html::a(
            Html::encode($mapName),
            ['show/user',
              'screen_name' => $user->screen_name,
              'filter' => [
                'rule' => '@gachi',
                'map' => $mapKey,
              ],
            ]
          ),
        ]),
        ['id' => 'wp-' . $mapKey]
      ) . "\n" ?>
<?php foreach ([null, 200] as $limit) { ?>
      <?= Html::tag('div', '', [
        'class' => 'graph stat-wp',
        'data' => array_filter([
          'map' => $mapKey,
          'limit' => $limit,
        ]),
      ]) . "\n" ?>
<?php } ?>
<?php } ?>
<?php $this->registerJs(vsprintf('$(%s).wp($(%s), %s, %s);', [
  Json::encode('.stat-wp'),
  Json::encode('#stat-wp-legend'),
  Json::encode($recentWP),
  Json::encode([
    'area' => sprintf('%s (%s)', Yii::t('app', 'Winning Percentage'), Yii::t('app-rule', 'Splat Zones')),
    'yagura' => sprintf('%s (%s)', Yii::t('app', 'Winning Percentage'), Yii::t('app-rule', 'Tower Control')),
    'hoko' => sprintf('%s (%s)', Yii::t('app', 'Winning Percentage'), Yii::t('app-rule', 'Rainmaker')),
    'moving20' => Yii::t('app', 'Win % ({0} Battles)', [20]),
    'moving50' => Yii::t('app', 'Win % ({0} Battles)', [50]),
  ]),
])) ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= $this->render("//includes/user-miniinfo", ["user" => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
