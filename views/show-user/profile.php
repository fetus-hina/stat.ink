<?php

declare(strict_types=1);

use app\assets\AppLinkAsset;
use app\components\helpers\T;
use app\components\widgets\ActivityWidget;
use app\components\widgets\UserIcon;
use app\components\widgets\battle\PanelListWidget;
use app\models\Battle2;
use app\models\Battle;
use app\models\Salmon2;
use app\models\User;
use app\models\query\Battle2Query;
use app\models\query\BattleQuery;
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var string $permLink
 */

$this->context->layout = 'main';

$fa = fn (string $icon, string $category = 'fa'): string => Html::tag(
  'span',
  '',
  [
    'class' => [
      $category,
      'fa-fw',
      'fa-' . $icon,
    ],
  ]
);

$title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->userIcon->absUrl ?? $user->jdenticonPngUrl]);
if ($user->twitter != '') {
    $this->registerMetaTag(['name' => 'twitter:creator', 'content' => sprintf('@%s', $user->twitter)]);
}

$css = [
  '#person-box h1' => [
    'font-size' => '30px',
    'margin' => '15px 0 5px',
    'font-weight' => '600',
  ],
  '#person-box h2' => [
    'font-weight' => '300',
    'font-size' => '24px',
    'margin' => 0,
  ],
  '#person-box ul, #person-box li' => [
    'display' => 'block',
    'list-style-type' => 'none',
    'margin' => 0,
    'padding' => 0,
  ],
  '#profile .tab-content' => [
    'margin-top' => '15px',
  ],
];
$this->registerCss(implode('', array_map(
  function ($key, $value) {
      return sprintf(
      '%s{%s}',
      $key,
      Html::cssStyleFromArray($value)
    );
  },
  array_keys($css),
  array_values($css)
)));
?>
<div id="profile" class="container">
  <div class="row">
    <div id="person-box" class="col-xs-12 col-md-3" itemscope itemtype="http://schema.org/Person">
      <?= UserIcon::widget([
        'user' => $user,
        'options' => [
          'class' => [
            'img-responsive',
            'img-thumbnail',
            'img-rounded',
            'w-100',
          ],
        ],
      ]) . "\n" ?>
      <h1 itemprop="name">
        <?= Html::encode($user->name) . "\n" ?>
      </h1>
      <h2 itemprop="alternateName">
        <?= Html::encode('@' . $user->screen_name) . "\n" ?>
      </h2>
<?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $user->id): ?>
      <div class="text-right">
        <?= Html::a(
          Yii::t('app', 'Edit'),
          ['/user/profile'],
          ['class' => 'btn btn-default']
        ) . "\n" ?>
      </div>
<?php endif; ?>
      <hr>
      <ul>
<?php if ($user->twitter): ?>
        <li>
          <?= $fa('twitter', 'fab') ?><?= Html::a(
            '@' . Html::encode($user->twitter),
            sprintf('https://twitter.com/%s', rawurlencode($user->twitter)),
            ['rel' => 'nofollow', 'target' => '_blank']
          ) . "\n" ?>
        </li>
<?php endif; ?>
<?php if ($user->nnid): ?>
<?php $asset = AppLinkAsset::register($this) ?>
        <li>
          <span class="fa fa-fw"><?= $asset->getNnid() ?></span>
          <?= Html::encode($user->nnid) . "\n" ?>
        </li>
<?php endif; ?>
<?php if ($user->sw_friend_code): ?>
<?php $asset = AppLinkAsset::register($this) ?>
        <li>
          <span class="fa fa-fw"><?= $asset->getSwitch() ?></span>
          <?= Html::encode(implode('-', [
            'SW',
            substr($user->sw_friend_code, 0, 4),
            substr($user->sw_friend_code, 4, 4),
            substr($user->sw_friend_code, 8, 4),
          ])) . "\n" ?>
        </li>
<?php endif; ?>
<?php if ($user->ikanakama2): ?>
<?php $asset = AppLinkAsset::register($this) ?>
        <li>
          <span class="fa fa-fw"><?= $asset->getIkanakama() ?></span>
          <?= Html::a(
            Yii::t('app', 'Ika-Nakama 2'),
            sprintf('https://ikanakama.ink/users/%d', $user->ikanakama2),
            ['rel' => 'nofollow', 'target' => '_blank']
          ) . "\n" ?>
        </li>
<?php endif; ?>
    </div>
    <div class="col-xs-12 col-md-9">
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
          <a href="#splatoon2" aria-controls="splatoon2" role="tab" data-toggle="tab">Splatoon 2</a>
        </li>
        <li role="presentation">
          <a href="#splatoon" aria-controls="splatoon" role="tab" data-toggle="tab">Splatoon</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="splatoon2">
          <?= $this->render(
            '@app/views/includes/battles-summary',
            [
              'summary' => T::is(Battle2Query::class, $user->getBattle2s())->getSummary(),
              'link' => ['show-v2/user', 'screen_name' => $user->screen_name],
            ]
          ) . "\n" ?>
          <div class="row">
<?php if ($user->getSalmonResults()->exists()) { ?>
            <div class="col-xs-12 col-sm-6">
              <?= PanelListWidget::widget([
                'title' => Yii::t('app', 'Battles'),
                'titleLink' => [
                    '/show-v2/user',
                    'screen_name' => $user->screen_name,
                ],
                'titleLinkText' => Yii::t('app', 'List'),
                'models' => Battle2::find()
                    ->with(['user', 'map', 'weapon'])
                    ->innerJoinWith(['mode', 'rule'])
                    ->andWhere(['battle2.user_id' => $user->id])
                    ->orderBy(['battle2.id' => SORT_DESC])
                    ->limit(5)
                    ->all(),
              ]) . "\n" ?>
            </div><!-- col -->
            <div class="col-xs-12 col-sm-6">
              <?= PanelListWidget::widget([
                'title' => Yii::t('app-salmon2', 'Salmon Run'),
                'titleLink' => [
                    'salmon/index',
                    'screen_name' => $user->screen_name,
                ],
                'titleLinkText' => Yii::t('app', 'List'),
                'models' => Salmon2::find()
                    ->andWhere(['user_id' => $user->id])
                    ->with(['players', 'stage', 'user'])
                    ->orderBy(['id' => SORT_DESC])
                    ->limit(5)
                    ->all(),
              ]) . "\n" ?>
            </div><!-- col -->
<?php } else { ?>
            <div class="col-xs-12 col-sm-6">
              <?= PanelListWidget::widget([
                'title' => Yii::t('app-rule2', 'Turf War'),
                'titleLink' => [
                    '/show-v2/user',
                    'screen_name' => $user->screen_name,
                    'filter' => [
                        'rule' => 'standard-regular-nawabari',
                    ],
                ],
                'titleLinkText' => Yii::t('app', 'List'),
                'models' => Battle2::find()
                    ->with(['user', 'map', 'weapon'])
                    ->innerJoinWith(['mode', 'rule'])
                    ->andWhere([
                        'battle2.user_id' => $user->id,
                        'rule2.key' => 'nawabari',
                    ])
                    ->orderBy(['battle2.id' => SORT_DESC])
                    ->limit(5)
                    ->all(),
              ]) . "\n" ?>
            </div><!-- col -->
            <div class="col-xs-12 col-sm-6">
              <?= PanelListWidget::widget([
                'title' => Yii::t('app-rule2', 'Ranked Battle'),
                'titleLink' => [
                    '/show-v2/user',
                    'screen_name' => $user->screen_name,
                    'filter' => [
                        'rule' => 'any-gachi-any',
                    ],
                ],
                'titleLinkText' => Yii::t('app', 'List'),
                'models' => Battle2::find()
                    ->with(['user', 'map', 'weapon'])
                    ->innerJoinWith(['mode', 'rule'])
                    ->andWhere(['and',
                        ['battle2.user_id' => $user->id],
                        ['<>', '{{rule2}}.[[key]]', 'nawabari'],
                    ])
                    ->orderBy(['battle2.id' => SORT_DESC])
                    ->limit(5)
                    ->all(),
              ]) . "\n" ?>
            </div><!-- col -->
<?php } ?>
          </div><!-- row -->
        </div><!-- tabpanel -->
        <div role="tabpanel" class="tab-pane" id="splatoon">
          <?= $this->render(
            '@app/views/includes/battles-summary',
            [
              'summary' => T::is(BattleQuery::class, $user->getBattles())->getSummary(),
              'link' => ['show/user', 'screen_name' => $user->screen_name],
            ]
          ) . "\n" ?>
          <div class="row">
            <div class="col-xs-12 col-sm-6">
              <?= PanelListWidget::widget([
                'title' => Yii::t('app-rule', 'Turf War'),
                'titleLink' => [
                    '/show/user',
                    'screen_name' => $user->screen_name,
                    'filter' => [
                        'rule' => 'nawabari'
                    ],
                ],
                'titleLinkText' => Yii::t('app', 'List'),
                'models' => Battle::find()
                    ->with(['user', 'map', 'weapon'])
                    ->innerJoinWith(['lobby', 'rule'])
                    ->andWhere([
                        'battle.user_id' => $user->id,
                        'rule.key' => 'nawabari',
                    ])
                    ->orderBy(['battle.id' => SORT_DESC])
                    ->limit(5)
                    ->all(),
              ]) . "\n" ?>
            </div><!-- col -->
            <div class="col-xs-12 col-sm-6">
              <?= PanelListWidget::widget([
                'title' => Yii::t('app-rule', 'Ranked Battle'),
                'titleLink' => [
                    '/show/user',
                    'screen_name' => $user->screen_name,
                    'filter' => [
                        'rule' => '@gachi'
                    ],
                ],
                'titleLinkText' => Yii::t('app', 'List'),
                'models' => Battle::find()
                    ->with(['user', 'map', 'weapon'])
                    ->innerJoinWith(['lobby', 'rule'])
                    ->andWhere([
                        'battle.user_id' => $user->id,
                        'rule.key' => ['area', 'yagura', 'hoko'],
                    ])
                    ->orderBy(['battle.id' => SORT_DESC])
                    ->limit(5)
                    ->all(),
              ]) . "\n" ?>
            </div><!-- col -->
          </div><!-- row -->
        </div><!-- tabpanel -->
        <div class="row"><!-- row -->
          <div class="col-xs-12" id="activity">
            <div class="panel panel-default">
              <div class="panel-heading">
                <?= Html::encode(Yii::t('app', 'Activity')) . "\n" ?>
              </div>
              <div class="panel-body bg-white">
                <div class="table-responsive">
                  <?= ActivityWidget::widget([
                    'user' => $user,
                  ]) . "\n" ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
