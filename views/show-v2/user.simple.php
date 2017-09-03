<?php
use app\components\widgets\AdWidget;
use app\components\widgets\Battle2FilterWidget;
use app\components\widgets\SnsWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

$title = Yii::t('app', "{0}'s Splat Log", $user->name);
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
    'feedUrl' => Url::to(['feed/user-v2', 'screen_name' => $user->screen_name, 'type' => 'rss', 'lang' => Yii::$app->language], true),
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
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
      <?= $this->render(
        '/includes/battles-summary',
        [
          'headingText' => Yii::t('app', 'Summary: Based on the current filter'),
          'summary' => $summary
        ]
      ) . "\n" ?>
      <div>
        <a href="#filter-form" class="visible-xs-inline-block btn btn-info">
          <span class="fa fa-search fa-fw"></span><?= Html::encode(Yii::t('app', 'Search')) . "\n" ?>
        </a>
        <?= Html::a(
          '<span class="fa fa-list fa-fw"></span> ' . Html::encode(Yii::t('app', 'Detailed List')),
          array_merge($filter->toQueryParams(), ['show-v2/user', 'screen_name' => $user->screen_name, 'v' => 'standard']),
          ['class' => 'btn btn-default', 'rel' => 'nofollow']
        ) . "\n" ?>
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
<?php $this->registerCss(sprintf('.simple-battle-list{%s}', Html::cssStyleFromArray([
  'display' => 'block',
  'list-style-type' => '0',
  'margin' => '0',
  'padding' => '0',
]))) ?>
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
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= Battle2FilterWidget::widget([
        'route' => 'show-v2/user',
        'screen_name' => $user->screen_name,
        'filter' => $filter,
      ]) . "\n" ?>
      <?= $this->render('/includes/user-miniinfo2', ['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
