<?php
declare(strict_types=1);

use app\actions\entire\KDWinAction;
use app\assets\EntireKDWinAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\kdWin\KDWinTable;
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

TableResponsiveForceAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>
  <p><?= Html::encode(Yii::t(
    'app',
    'This website has color-blind support. Please check "Color-Blind Support" in the "Username/Guest" menu of the navbar to enable it.'
  )) ?></p>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <nav>
    <ul class="nav nav-tabs" style="margin-bottom:15px">
      <li><?= Html::a(
        Html::encode('Splatoon 3'),
        ['entire/kd-win3'],
      ) ?></li>
      <li><?= Html::a(
        Html::encode('Splatoon 2'),
        ['entire/kd-win2'],
      ) ?></li>
      <li class="active"><a><?= Html::encode('Splatoon') ?></a></li>
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
    <div class="col-xs-12">
      <?= Html::tag(
        'h2',
        Html::encode($rule->name),
        ['class' => $rule->key, 'id' => $rule->key]
      ) . "\n" ?>
      <div class="table-responsive table-responsive-force">
        <?= KDWinTable::widget([
          'data' => $rule->data,
          'limit' => KDWinAction::KD_LIMIT,
        ]) . "\n" ?>
      </div>
    </div>
  </div>
<?php } ?>
</div>
