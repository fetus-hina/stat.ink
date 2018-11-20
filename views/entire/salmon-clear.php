<?php
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotPieAsset;
use yii\helpers\Html;

$title = Yii::t('app-salmon2', 'Clear rate of Salmon Run');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$this->registerCss('.graph{height:200px;width:200px}');

FlotAsset::register($this);
FlotPieAsset::register($this);

$this->registerJs(<<<'EOF'
$('.clear-rate').each(function(){
  var $this = $(this);
  $.plot($this,JSON.parse($this.attr('data-data')),{
    series: {
      pie: {
        show: true,
        radius:1,
        label: {
          show: "auto",
          radius: 0.7,
          formatter: function (label, slice) {
            return $('<div>').append(
              $('<div>').css({
                'fontSize': '0.618em',
                'lineHeight': '1.1em',
                'textAlign': 'center',
                'padding': '2px',
                'color': '#000',
                'textShadow': '0px 0px 3px #fff'
              }).append(
                label
              ).append(
                $('<br>')
              ).append(
                slice.percent.toFixed(1) + '%'
              )
            ).html();
          }
        }
      }
    },
    legend: {
      show: false,
    }
  });
});
EOF
);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>
<?php foreach ($models as $model) { ?>
<?php if ($model->jobs > 0) { ?>
  <?= Html::tag(
    'h2',
    Html::encode(Yii::t('app-salmon-map2', $model->stage->name)),
    ['id' => $model->stage->key]
  ) . "\n" ?>
  <?= Html::tag('div', '', [
    'class' => ['graph', 'clear-rate'],
    'data' => [
      'data' => [
        [
          'label' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', ['waveNumber' => 1]),
          'data' => (int)$model->w1_failed,
        ],
        [
          'label' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', ['waveNumber' => 2]),
          'data' => (int)$model->w2_failed,
        ],
        [
          'label' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', ['waveNumber' => 3]),
          'data' => (int)$model->w3_failed,
        ],
        [
          'label' => Yii::t('app-salmon2', 'Cleared'),
          'data' => (int)$model->cleared,
        ],
      ],
    ],
  ]) . "\n" ?>
  <p><i><small class="text-muted">n=<?= Yii::$app->formatter->asInteger($model->jobs) ?></small></i></p>
<?php } ?>
<?php } ?>
</div>
