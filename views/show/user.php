<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\assets\BattleListAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\BattleFilterWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Language;
use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var User $user
 * @var View $this
 */

BattleListAsset::register($this);

$title = Yii::t('app', '{name}\'s Splat Log', ['name' => $user->name]);
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);

if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

foreach (Language::find()->standard()->all() as $lang) {
  $this->registerLinkTag([
    'rel' => 'alternate',
    'type' => 'application/rss+xml',
    'title' => sprintf('%s - RSS Feed (%s)', $title, $lang->name),
    'href' => Url::to(
      ['feed/user',
        'screen_name' => $user->screen_name,
        'type' => 'rss',
        'lang' => $lang->lang,
      ],
      true
    ),
    'hreflang'  => $lang->lang,
  ]);
  $this->registerLinkTag([
    'rel' => 'alternate',
    'type' => 'application/atom+xml',
    'title' => sprintf('%s - Atom Feed (%s)', $title, $lang->name),
    'href' => Url::to(
      ['feed/user',
        'screen_name' => $user->screen_name,
        'type' => 'atom',
        'lang' => $lang->lang,
      ],
      true
    ),
    'hreflang'  => $lang->lang,
  ]);
}

$battle = $user->latestBattle;
$f = Yii::$app->formatter;
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>
  
<?php
if ($battle &&
    $battle->agent &&
    $battle->agent->isIkaLog &&
    $battle->agent->getIsOldIkalogAsAtTheTime($battle->at)
) { ?>
<?php $this->registerCss('.old-ikalog{font-weight:bold;color:#f00}') ?>
  <p class="old-ikalog">
    <?= Html::encode(
      Yii::t(
        'app',
        'These battles were recorded with an outdated version of IkaLog. Please upgrade to the latest version.'
      )
    ) . "\n" ?>
  </p>
<?php } ?>

  <?= SnsWidget::widget([
    'feedUrl' => Url::to(
      ['feed/user',
        'screen_name' => $user->screen_name,
        'type' => 'rss',
        'lang' => Yii::$app->language,
      ],
      true
    ),
    'tweetText' => sprintf(
      '%s [ %s ]',
      $title,
      Yii::t(
        'app',
        'Battles:{0} / Win %:{1} / Avg Kills:{2} / Avg Deaths:{3} / Kill Ratio:{4}',
        [
          $f->asInteger($summary->battle_count),
          $summary->wp === null ? '-' : $f->asPercent($summary->wp / 100, 1),
          $summary->kd_present > 0
            ? $f->asDecimal($summary->total_kill / $summary->kd_present, 2)
            : '-',
          $summary->kd_present > 0
            ? $f->asDecimal($summary->total_death / $summary->kd_present, 2)
            : '-',
          $summary->kd_present > 0
            ? ($summary->total_death > 0
              ? $f->asDecimal($summary->total_kill / $summary->total_death, 2)
              : ($summary->total_kill > 0
                ? '∞'
                : '-'
              )
            )
            : '-',
        ]
      )
    ),
  ]) . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <div class="text-right">
        <?= ListView::widget([
          'dataProvider' => $battleDataProvider,
          'itemView' => '_battle.tablerow.php',
          'itemOptions' => [ 'tag' => false ],
          'layout' => '{pager}',
          'pager' => [
            'maxButtonCount' => 5
          ]
        ]) . "\n" ?>
      </div>
      <?= $this->render(
        '//includes/battles-summary',
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
          ['class' => 'visible-xs-inline btn btn-info'],
        ) . "\n" ?>
        <?= Html::a(
          implode(' ', [
            '<span class="fas fa-fw fa-cogs"></span>',
            Html::encode(Yii::t('app', 'View Settings')),
          ]),
          '#table-config',
          ['class' => 'btn btn-default'],
        ) . "\n" ?>
        <?= Html::a(
          '<span class="fa fa-fw fa-list"></span>' . Html::encode(Yii::t('app', 'Simplified List')),
          array_merge($filter->toQueryParams(), ['show/user', 'v' => 'simple']),
          ['class' => 'btn btn-default', 'rel' => 'nofollow']
        ) . "\n" ?>
      </div>
      <div class="table-responsive" id="battles">
        <table class="table table-striped table-condensed">
          <thead>
            <tr>
              <th></th>
              <th class="cell-lobby"><?= Html::encode(Yii::t('app', 'Lobby')) ?></th>
              <th class="cell-rule"><?= Html::encode(Yii::t('app', 'Mode')) ?></th>
              <th class="cell-rule-short"><?= Html::encode(Yii::t('app', 'Mode')) ?></th>
              <th class="cell-map"><?= Html::encode(Yii::t('app', 'Stage')) ?></th>
              <th class="cell-map-short"><?= Html::encode(Yii::t('app', 'Stage')) ?></th>
              <th class="cell-main-weapon"><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
              <th class="cell-main-weapon-short"><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
              <th class="cell-sub-weapon"><?= Html::encode(Yii::t('app', 'Sub Weapon')) ?></th>
              <th class="cell-special"><?= Html::encode(Yii::t('app', 'Special')) ?></th>
              <th class="cell-rank"><?= Html::encode(Yii::t('app', 'Rank')) ?></th>
              <th class="cell-rank-after"><?= Html::encode(Yii::t('app', 'Rank (After)')) ?></th>
              <th class="cell-level"><?= Html::encode(Yii::t('app', 'Level')) ?></th>
              <th class="cell-result"><?= Html::encode(Yii::t('app', 'Result')) ?></th>
              <th class="cell-kd"><?= Html::encode(Yii::t('app', 'k')) ?>/<?= Html::encode(Yii::t('app', 'd')) ?></th>
              <th class="cell-kill-ratio auto-tooltip" title="<?= Html::encode(Yii::t('app', 'Kill Ratio')) ?>"><?= Html::encode(Yii::t('app', 'Ratio')) ?></th>
              <th class="cell-kill-rate auto-tooltip" title="<?= Html::encode(Yii::t('app', 'Kill Rate')) ?>"><?= Html::encode(Yii::t('app', 'Rate')) ?></th>
              <th class="cell-point"><?= Html::encode(Yii::t('app', 'Inked')) ?></th>
              <th class="cell-rank-in-team"><?= Html::encode(Yii::t('app', 'Rank in Team')) ?></th>
              <th class="cell-datetime"><?= Html::encode(Yii::t('app', 'Date Time')) ?></th>
              <th class="cell-reltime"><?= Html::encode(Yii::t('app', 'Relative Time')) ?></th>
            </tr>
          </thead>
          <tbody>
            <?= ListView::widget([
              'dataProvider' => $battleDataProvider,
              'itemView' => '_battle.tablerow.php',
              'itemOptions' => [ 'tag' => false ],
              'layout' => '{items}'
            ]) . "\n" ?>
          </tbody>
        </table>
      </div>
      <div class="text-right">
        <?= ListView::widget([
          'dataProvider' => $battleDataProvider,
          'itemView' => '_battle.tablerow.php',
          'itemOptions' => [ 'tag' => false ],
          'layout' => '{pager}',
          'pager' => [
            'maxButtonCount' => 5
          ]
        ]) . "\n" ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= BattleFilterWidget::widget(['route' => 'show/user', 'screen_name' => $user->screen_name, 'filter' => $filter]) . "\n" ?>
      <?= $this->render('//includes/user-miniinfo', ['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="table-config">
      <div>
        <label>
          <input type="checkbox" id="table-hscroll" value="1"> <?= Html::encode(Yii::t('app', 'Always enable horizontal scroll')) . "\n" ?>
        <label>
      </div>
      <div class="row">
<?php $_list = [
  [
    'class' => 'cell-lobby',
    'text' => Yii::t('app', 'Lobby'),
  ],
  [
    'class' => 'cell-rule',
    'text' => Yii::t('app', 'Mode'),
  ],
  [
    'class' => 'cell-rule-short',
    'text' => Yii::t('app', 'Mode (Short)'),
  ],
  [
    'class' => 'cell-map',
    'text' => Yii::t('app', 'Stage'),
  ],
  [
    'class' => 'cell-map-short',
    'text' => Yii::t('app', 'Stage (Short)'),
  ],
  [
    'class' => 'cell-main-weapon',
    'text' => Yii::t('app', 'Weapon'),
  ],
  [
    'class' => 'cell-main-weapon-short',
    'text' => Yii::t('app', 'Weapon (Short)'),
  ],
  [
    'class' => 'cell-sub-weapon',
    'text' => Yii::t('app', 'Sub Weapon'),
  ],
  [
    'class' => 'cell-special',
    'text' => Yii::t('app', 'Special'),
  ],
  [
    'class' => 'cell-rank',
    'text' => Yii::t('app', 'Rank'),
  ],
  [
    'class' => 'cell-rank-after',
    'text' => Yii::t('app', 'Rank (After)'),
  ],
  [
    'class' => 'cell-level',
    'text' => Yii::t('app', 'Level'),
  ],
  [
    'class' => 'cell-result',
    'text' => Yii::t('app', 'Result'),
  ],
  [
    'class' => 'cell-kd',
    'text' => Yii::t('app', 'k') . '/' . Yii::t('app', 'd'),
  ],
  [
    'class' => 'cell-kill-ratio',
    'text' => Yii::t('app', 'Kill Ratio'),
  ],
  [
    'class' => 'cell-kill-rate',
    'text' => Yii::t('app', 'Kill Rate'),
  ],
  [
    'class' => 'cell-point',
    'text' => Yii::t('app', 'Turf Inked'),
  ],
  [
    'class' => 'cell-rank-in-team',
    'text' => Yii::t('app', 'Rank in Team'),
  ],
  [
    'class' => 'cell-datetime',
    'text' => Yii::t('app', 'Date Time'),
  ],
  [
    'class' => 'cell-reltime',
    'text' => Yii::t('app', 'Relative Time'),
  ],
] ?>
<?php foreach ($_list as $_item): ?>
        <?= Html::tag(
          'div',
          Html::tag('label', implode(' ', [
            Html::tag('input', '', [
              'type' => 'checkbox',
              'class' => 'table-config-chk',
              'data-klass' => $_item['class'],
            ]),
            Html::encode($_item['text']),
          ])),
          ['class' => 'col-xs-6 col-sm-4 col-lg-3']
        ) . "\n" ?>
<?php endforeach ?>
      </div>
    </div>
  </div>
</div>
