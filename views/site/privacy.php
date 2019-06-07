<?php
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;

$title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Privacy Policy'),
]);
$this->context->layout = 'main';
$this->title = $title;
?>
<div class="container">
  <h1>
    <?= Html::encode(Yii::t('app', 'Privacy Policy')) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p>
    <?= Html::encode(Yii::t('app', 'This website ({0}) collects the following data:', [Yii::$app->name])) . "\n" ?>
  </p>
  <ul>
<?php $list = [
  'Access time',
  'IP address',
  'The address of the web site that linked here (aka "referer")',
  'Your OS, browser name, and version that you are using (aka "user agent")',
] ?>
<?php foreach ($list as $_) { ?>
    <?= Html::tag('li', Html::encode(Yii::t('app', $_))) . "\n" ?>
<?php } ?>
  </ul>
  <p><?= Html::encode(
    Yii::t('app', 'This website uses cookies to track your session or save your configuration (e.g., language / time zone settings).')
  ) ?></p>
  <p>
    <?= Html::encode(Yii::t('app', 'We don\'t release your collected information, like your IP address. However, statistical information will be released.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app', 'If there is an investigation being conducted by the police or other authority, your information will be released.')) . "\n" ?>
  </p>

  <h2 id="image">
    <?= Html::encode(Yii::t('app', 'About image sharing with the IkaLog team')) . "\n" ?>
  </h2>
  <p>
    <?= Html::encode(Yii::t('app', 'Your uploaded data (battle stats, images, and modification history) will be shared with the IkaLog development team.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app', 'This is done automatically and the data will not be deleted even if the the battle is deleted.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app', 'This behavior was started on 27 Oct 2015.')) . "\n" ?>
  </p>
</div>
