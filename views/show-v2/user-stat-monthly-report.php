<?php

declare(strict_types=1);

use app\assets\UserStat2MonthlyReportAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Battle2;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var int $month
 * @var int $year
 */

$title = Yii::t('app', "{name}'s Monthly Report - {date}", [
  'name' => $user->name,
  'date' => Yii::$app->formatter->asDate(
    sprintf('%d-%02d', $year, $month),
    Yii::t('app', 'MMMM y')
  ),
]);
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->getIconUrl()]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}
if ($next) {
  $this->registerLinkTag(['rel' => 'next', 'href' => $next]);
}
if ($prev) {
  $this->registerLinkTag(['rel' => 'prev', 'href' => $prev]);
}

UserStat2MonthlyReportAsset::register($this);

$modes = [
    'nawabari' => [
        'name' => 'Turf War',
        'details' => null,
    ],
    'gachi' => [
        'name' => 'Ranked Battle',
        'details' => null,
    ],
    'league' => [
        'name' => 'League Battle',
        'details' => [
            'league2' => 'League Battle (Twin)',
            'league4' => 'League Battle (Quad)',
        ],
    ],
    'private' => [
        'name' => 'Private Battle',
        'details' => ['private'],
    ],
];

?>
<div class="container">
  <?= Html::tag(
    'h1',
    Yii::t('app', "{name}'s Monthly Report - {date}", [
      'name' => Html::a(
        Html::encode($user->name),
        ['show-v2/user', 'screen_name' => $user->screen_name]
      ),
      'date' => Html::encode(
        Yii::$app->formatter->asDate(
          sprintf('%d-%02d', $year, $month),
          Yii::t('app', 'MMMM y')
        )
      ),
    ])
  ) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

<?php if ($next || $prev) { ?>
  <div class="row mb-3">
    <div class="col-xs-6">
<?php if ($prev) { ?>
      <?= Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fas fa-fw fa-angle-double-left']),
          Html::encode(Yii::t('app', 'Prev. Month')),
        ]),
        $prev,
        ['class' => 'btn btn-default']
      ) . "\n" ?>
<?php } ?>
    </div>
    <div class="col-xs-6 pull-right text-right">
<?php if ($next) { ?>
      <?= Html::a(
        implode('', [
          Html::encode(Yii::t('app', 'Next Month')),
          Html::tag('span', '', ['class' => 'fas fa-fw fa-angle-double-right']),
        ]),
        $next,
        ['class' => 'btn btn-default']
      ) . "\n" ?>
<?php } ?>
    </div>
  </div>
<?php } ?>

  <h2><?= Html::encode(Yii::t('app', 'Win %')) ?></h2>
  <div class="row">
<?php foreach ($modes as $modeKey => $modeInfo) { ?>
<?php $item = ArrayHelper::getValue($abstract, $modeKey, null) ?>
    <div class="col-12 col-md-6 col-lg-3 mb-3">
      <h3 class="mt-0">
<?php if ($item && $modeKey !== 'private' && $modeKey !== 'nawabari') { ?>
        <?= Html::tag(
          'a',
          Html::tag('span', '', ['class' => 'fas fa-angle-down']),
          ['href' => '#' . $modeKey]
        ) . "\n" ?>
<?php } ?>
        <?= Html::encode(Yii::t('app-rule2', $modeInfo['name'])) . "\n" ?>
      </h3>
<?php if ($item) { ?>
      <?= $this->render('//show-v2/user-stat-monthly-report/win-pct', [
        'battles' => $item->battles,
        'wins' => $item->wins,
      ]) . "\n" ?>
<?php } else { ?>
      <p class="text-muted m-0">
        <?= Html::encode(Yii::t('app', 'No Data')) . "\n" ?>
      </p>
<?php } ?>
    </div>
<?php } ?>
  </div>

<?php
foreach ($modes as $modeKey => $modeInfo) {
  if ($modeKey !== 'private' && $modeKey !== 'nawabari' && isset($abstract[$modeKey])) {
    if ($modeInfo['details']) {
      echo Html::beginTag('div', ['id' => $modeKey]) . "\n";
      foreach ($modeInfo['details'] as $detailKey => $detailName) {
        if (isset($rulesAndStages[$detailKey])) {
          echo '<hr>';
          echo $this->render('//show-v2/user-stat-monthly-report/mode', [
            'modeKey' => $detailKey,
            'modeName' => $detailName,
            'data' => $rulesAndStages[$detailKey],
          ]) . "\n";
        }
      }
      echo Html::endTag('div') . "\n";
    } else {
      echo '<hr>';
      echo $this->render('//show-v2/user-stat-monthly-report/mode', [
        'modeKey' => $modeKey,
        'modeName' => $modeInfo['name'],
        'data' => $rulesAndStages[$modeKey],
      ]) . "\n";
    }
  }
}
?>
</div>
