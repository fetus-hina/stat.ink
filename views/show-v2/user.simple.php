<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\SimpleBattleListAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Battle2FilterWidget;
use app\components\widgets\FA;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo2;
use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/**
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag([
  'name' => 'twitter:image',
  'content' => $user->iconUrl,
]);
if ($user->twitter != '') {
  $this->registerMetaTag([
    'name' => 'twitter:creator',
    'content' => sprintf('@%s', $user->twitter),
  ]);
}

SimpleBattleListAsset::register($this);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= SnsWidget::widget([
    'tweetText' => (function () use ($title, $summary) {
      $fmt = Yii::$app->formatter;
      return sprintf(
        '%s [ %s ]',
        $title,
        Yii::t('app', 'Battles:{0} / Win %:{1} / Avg Kills:{2} / Avg Deaths:{3} / Kill Ratio:{4}', [
          $fmt->asInteger($summary->battle_count),
          $summary->wp === null ? '-' : $fmt->asPercent($summary->wp / 100, 1),
          $summary->kd_present > 0 ? $fmt->asDecimal($summary->total_kill / $summary->kd_present, 2) : '-',
          $summary->kd_present > 0 ? $fmt->asDecimal($summary->total_death / $summary->kd_present, 2) : '-',
          $summary->kd_present > 0
            ? ($summary->total_death == 0
              ? ($summary->total_kill == 0 ? '-' : '∞')
              : $fmt->asDecimal($summary->total_kill / $summary->total_death, 2)
            )
            : '-',
        ])
      );
    })(),
    'feedUrl' => Url::to(
      ['feed/user-v2',
        'screen_name' => $user->screen_name,
        'type' => 'rss',
        'lang' => preg_replace('/@.+$/', '', Yii::$app->language),
      ],
      true
    ),
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <p class="text-right">
        <?= Html::beginTag('a', [
          'class' => 'btn btn-default btn-xs',
          'href' => Url::to(['salmon/index', 'screen_name' => $user->screen_name]),
        ]) . "\n" ?>
          <?= FA::fas('fish')->fw() . "\n" ?>
          <?= Html::encode(Yii::t('app-salmon2', 'Salmon Run')) . "\n" ?>
          <?= FA::fas('angle-right')->fw() . "\n" ?>
        </a>
      </p>
      <?= $this->render(
        '/includes/battles-summary',
        [
          'headingText' => Yii::t('app', 'Summary: Based on the current filter'),
          'summary' => $summary
        ]
      ) . "\n" ?>
      <div>
        <?= Html::a(
          implode(' ', [
            Icon::search(),
            Html::encode(Yii::t('app', 'Search')),
          ]),
          '#filter-form',
          ['class' => 'visible-xs-inline-block btn btn-info']
        ) . "\n" ?>
        <?= Html::a(
          implode(' ', [
            (string)FA::fas('list')->fw(),
            Html::encode(Yii::t('app', 'Detailed List')),
          ]),
          array_merge($filter->toQueryParams(), ['show-v2/user', 'screen_name' => $user->screen_name, 'v' => 'standard']),
          ['class' => 'btn btn-default', 'rel' => 'nofollow']
        ) . "\n" ?>
      </div>
      <?php Pjax::begin(); echo "\n" ?>
        <div class="text-center">
          <?= ListView::widget([
            'dataProvider' => $battleDataProvider,
            'itemOptions' => [ 'tag' => false ],
            'layout' => '{pager}',
            'pager' => [
              'maxButtonCount' => 5
            ]
          ]) . "\n" ?>
        </div>
        <div id="battles">
          <ul class="simple-battle-list">
            <?= ListView::widget([
              'dataProvider' => $battleDataProvider,
              'itemView' => 'battle.simple.tablerow.php',
              'itemOptions' => [ 'tag' => false ],
              'layout' => '{items}'
            ]) . "\n" ?>
          </ul>
        </div>
        <div class="text-center">
          <?= ListView::widget([
            'dataProvider' => $battleDataProvider,
            'itemOptions' => [ 'tag' => false ],
            'layout' => '{pager}',
            'pager' => [
              'maxButtonCount' => 5
            ]
          ]) . "\n" ?>
        </div>
      <?php Pjax::end(); echo "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= Battle2FilterWidget::widget([
        'route' => 'show-v2/user',
        'screen_name' => $user->screen_name,
        'connectivity' => true,
        'filter' => $filter,
        'withTeam' => true,
      ]) . "\n" ?>
      <?= UserMiniInfo2::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
