<?php

declare(strict_types=1);

use app\models\SlackAddForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var SlackAddForm $form
 * @var View $this
 * @var array<int, string> $languages
 */

$title = Yii::t('app', 'Add Slack Integration');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <p>
    <?= Html::encode(Yii::t('app', 'To use Slack integration, you need to configure Slack\'s "Incoming Webhook" first.')) . "\n" ?>
    <?= Html::encode(Yii::t('app', '(For advanced users)')) . "\n" ?>
  </p>
  <p>
    <?= implode(', ', [
      Html::a(
        Html::encode(Yii::t('app', 'About Incoming Webhook')),
        'https://api.slack.com/incoming-webhooks',
        ['target' => '_blank']
      ),
      Html::a(
        Html::encode(Yii::t('app', 'Create new webhook')),
        'https://my.slack.com/services/new/incoming-webhook/',
        ['target' => '_blank']
      ),
    ]) . "\n" ?>
  </p>

  <?php $_ = ActiveForm::begin(['id' => 'add-form', 'action' => ['slack-add']]); echo "\n" ?>
    <?= $_->field($form, 'webhook_url')
      ->input('text', [
        'placeholder' => 'https://hooks.slack.com/services/AAAAAAAAA/BBBBBBBBB/CCCCCCCCCCCCCCCCCCCCCCCC',
      ])
      ->hint(Yii::t('app', 'You can specify Discord\'s Slack compatible endpoint URL as well.')) . "\n"
    ?>
    <?= $_->field($form, 'username')
      ->hint(Yii::t('app', 'If omitted, the name set in the webhook configuration will be used.')) . "\n"
    ?>
    <?= $_->field($form, 'icon')
      ->input('text', [
        'placeholder' => ':emoji:'
      ])
      ->hint(Yii::t('app', '<a href="http://www.emoji-cheat-sheet.com/" target="_blank">Cheat sheet</a>. If omitted, the default icon will be used.')) . "\n"
    ?>
    <?= $_->field($form, 'channel')
      ->input('text', [
        'placeholder' => '#splatoon'
      ])
      ->hint(Yii::t('app', 'If omitted, the channel set in the webhook configuration will be used.')) . "\n"
    ?>
    <?= $_->field($form, 'language_id')
      ->dropDownList($languages)
      ->hint(Yii::t('app', 'The post will be in the language set here.')) . "\n"
    ?>
    <?= Html::submitButton(
      Html::encode(Yii::t('app', 'Add')),
      ['class' => 'btn btn-lg btn-primary btn-block']
    ) . "\n" ?>
  <?php ActiveForm::end(); echo "\n" ?>

  <div style="margin-top:15px">
    <?= Html::a(
      Html::encode(Yii::t('app', 'Back')),
      ['user/profile'],
      ['class' => 'btn btn-lg btn-default btn-block']
    ) . "\n" ?>
  </div>
</div>
