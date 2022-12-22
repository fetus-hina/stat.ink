<?php

declare(strict_types=1);

use app\assets\InlineListAsset;
use app\assets\UserStatNawabariAsset;
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

$title = Yii::t('app', '{name}\'s Battle Stats (Turf War)', ['name' => $user->name]);
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);
$this->context->layout = 'main';
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

UserStatNawabariAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
      <h2><?= Html::encode(Yii::t('app', 'Turf Inked')) ?></h2>
      <p><?= Html::encode(Yii::t('app', 'Excluded: Private Battles')) ?></p>
      <aside>
        <nav>
<?php InlineListAsset::register($this) ?>
          <ul class="inline-list"><?= implode('', array_map(
            function (stdClass $map): string {
              return Html::tag('li', Html::a(
                Html::encode($map->name),
                sprintf('#inked-%s', $map->key)
              ));
            },
            $inked
          )) ?></ul>
        </nav>
      </aside>
<?php foreach ($inked as $map) { ?>
        <?= Html::tag(
          'h3',
          implode(' ', array_filter([
            Html::a(
              Html::encode($map->name),
              ['show/user',
                'screen_name' => $user->screen_name,
                'filter' => [
                  'rule' => 'nawabari',
                  'map' => $map->key,
                ],
              ]
            ),
            $map->area
              ? Html::encode(sprintf('(%sp)', Yii::$app->formatter->asInteger($map->area)))
              : null,
          ])),
          ['id' => sprintf('inked-%s', $map->key)]
        ) . "\n" ?>
        <p><?= vsprintf('%s %s', [
          Html::encode(Yii::t('app', 'Average:')),
          implode(', ', array_filter([
            $map->avgInked
              ? Html::encode(sprintf('%sp', Yii::$app->formatter->asDecimal($map->avgInked, 1)))
              : Html::encode(Yii::t('app', 'N/A')),
            ($map->avgInked && $map->area)
              ? Html::encode(Yii::$app->formatter->asPercent($map->avgInked / $map->area, 1))
              : null,
          ])),
        ]) ?></p>
        <?= Html::tag('div', '', [
          'class' => 'graph stat-inked',
          'id' => 'stat-inked-' . $map->key,
        ]) . "\n" ?>
<?php $this->registerJs(vsprintf('$(%s).turfInked(%s, %s, %s, %s);', [
  Json::encode('#stat-inked-' . $map->key),
  Json::encode(array_map(
    function (stdClass $item): array {
      return [$item->index, $item->inked];
    },
    array_reverse($inked[$map->key]->battles ?? [])
  )),
  Json::encode($map->stats),
  Json::encode($map->area),
  Json::encode([
    'turfInked' => Yii::t('app', 'Turf Inked'),
    'average' => Yii::t('app', 'Average'),
    'percentile' => Yii::t('app', '{lower}-{upper} percentile', [
        'lower' => 5,
        'upper' => 95,
    ]),
  ]),
])) ?>
<?php } ?>
      <hr>
      <h2 id="wp"><?= Html::encode(Yii::t('app', 'Winning Percentage')) ?></h2>
      <p><?= Html::encode(Yii::t('app', 'Excluded: Private Battles')) ?></p>
      <div id="stat-wp-legend"></div>
      <div class="graph stat-wp"></div>
      <div class="graph stat-wp" data-limit="200"></div>
<?php $this->registerJs(vsprintf('$(%s).wp($(%s), %s, %s);', [
  Json::encode('.stat-wp'),
  Json::encode('#stat-wp-legend'),
  Json::encode($wp),
  Json::encode([
    'wp'    => Yii::t('app', 'Winning Percentage'),
    'wp20'  => Yii::t('app', 'Win % ({0} Battles)', [20]),
    'wp50'  => Yii::t('app', 'Win % ({0} Battles)', [50]),
  ]),
])) ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= $this->render('//includes/user-miniinfo', ['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
