<?php
declare(strict_types=1);

use app\actions\entire\KDWinAction;
use app\assets\EntireKDWinAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\kdWin\LegendWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->context->layout ='main';

$title = Yii::t('app', 'Winning Percentage based on K/D');
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

EntireKDWinAsset::register($this);
TableResponsiveForceAsset::register($this);

$fmt = Yii::$app->formatter;
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>
  <p><?= Html::encode(Yii::t(
    'app',
    'This website has implemented support for color-blindness. Please check "Color-Blind Support" in the "User Name/Guest" menu of the navbar to enable it.'
  )) ?></p>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <nav>
    <ul class="nav nav-tabs" style="margin-bottom:15px">
      <li><?= Html::a(
        Html::encode('Splatoon 2'),
        ['entire/kd-win2'],
      ) ?></li>
      <li class="active"><?= Html::a(
        Html::encode('Splatoon'),
        'javascript:;',
      ) ?></li>
    </ul>
  </nav>

  <?php $_ = ActiveForm::begin([
    'id' => 'filter-form',
    'action' => ['entire/kd-win'],
    'method' => 'get',
    'layout' => 'inline',
  ]); echo "\n" ?>
    <?= implode(' ', [
      $_->field($filter, 'map')->dropDownList($maps)->label(false),
      $_->field($filter, 'weapon')->dropDownList($weapons)->label(false),
      Html::submitButton(
        Yii::t('app', 'Summarize'),
        ['class' => 'btn btn-primary']
      ),
    ]) . "\n" ?>
  <?php ActiveForm::end(); echo "\n" ?>

  <?= LegendWidget::widget() . "\n" ?>

<?php foreach ($rules as $rule) { ?>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <?= Html::tag(
        'h2',
        Html::encode($rule->name),
        ['class' => $rule->key]
      ) . "\n" ?>
      <div class="table-responsive table-responsive-force">
        <table class="table table-bordered table-condensed rule-table">
          <thead>
            <tr>
              <th class="text-center kdcell"><?= Html::encode(vsprintf('%s＼%s', [
                Yii::t('app', 'd'),
                Yii::t('app', 'k'),
              ])) ?></th>
              <?= implode('', array_map(
                function (int $k) use ($fmt): string {
                  return Html::tag(
                    'th',
                    Html::encode(implode('', [
                      $fmt->asInteger($k),
                      $k === KDWinAction::KD_LIMIT ? '+' : '',
                    ])),
                    ['class' => [
                      'text-center',
                      'kdcell',
                    ]]
                  );
                },
                range(0, KDWinAction::KD_LIMIT)
              )) . "\n" ?>
            </tr>
          </thead>
          <tbody>
<?php foreach (range(0, KDWinAction::KD_LIMIT) as $d) { ?>
            <tr>
              <th class="text-center kdcell"><?= Html::encode(implode('', [
                $fmt->asInteger($d),
                $d === KDWinAction::KD_LIMIT ? '+' : '',
              ])) ?></th>
<?php foreach (range(0, KDWinAction::KD_LIMIT) as $k) { ?>
<?php $data = $rule->data[$k][$d] ?>
              <?= Html::tag(
                'td',
                implode('<br>', [
                  Html::encode(sprintf(
                    '%s / %s',
                    $fmt->asInteger($data->win),
                    $fmt->asInteger($data->battle),
                  )),
                  Html::encode(
                    ($data->battle > 0)
                      ? $fmt->asPercent($data->win / $data->battle, 1)
                      : '-'
                  ),
                ]),
                [
                  'class' => [
                    'text-center',
                    'kdcell',
                    'percent-cell',
                  ],
                  'data' => [
                    'battle' => (string)(int)$data->battle,
                    'percent' => ($data->battle > 0)
                      ? ($data->win * 100 / $data->battle)
                      : '',
                  ],
                ]
              ) . "\n" ?>
<?php } ?>
            </tr>
<?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php } ?>
</div>
