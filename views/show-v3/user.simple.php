<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\SimpleBattleListAsset;
use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Battle3FilterWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\data\BaseDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/**
 * @var BaseDataProvider $battleDataProvider
 * @var Battle3FilterWidget $filter
 * @var User $user
 * @var View $this
 * @var array $summary
 * @var string $permLink
 */

$title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);

SimpleBattleListAsset::register($this);
OgpHelper::profileV3($this, $user, $permLink);

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
    'jsonUrl' => array_merge(
      $filter->toPermLink(),
      ['show-v3/user-json',
        'full' => preg_match('/^(?:en|ja)-/', (string)Yii::$app->language) ? null : '1',
        'page' => ArrayHelper::getValue(Yii::$app->request->get(), 'page'),
        'per-page' => ArrayHelper::getValue(Yii::$app->request->get(), 'per-page'),
        'screen_name' => $user->screen_name,
      ],
    ),
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= $this->render(
        '//includes/battles-summary',
        [
          'headingText' => Yii::t('app', 'Summary: Based on the current filter'),
          'summary' => $summary
        ]
      ) . "\n" ?>
      <?= Html::tag(
        'div',
        implode(' ', [
          Html::a(
            implode(' ', [
              Icon::search(),
              Html::encode(Yii::t('app', 'Search')),
            ]),
            '#filter-form',
            ['class' => 'visible-xs-inline-block btn btn-info'],
          ),
          Html::a(
            implode(' ', [
              Icon::list(),
              Html::encode(Yii::t('app', 'Detailed List')),
            ]),
            ['show-v3/user', 'screen_name' => $user->screen_name, 'v' => 'standard'],
            ['class' => 'btn btn-default', 'rel' => 'nofollow']
          ),
        ]),
      ) . "\n" ?>
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
      <?= Battle3FilterWidget::widget([
        'filter' => $filter,
        'playedWith' => true,
        'user' => $user,
      ]) . "\n" ?>
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
