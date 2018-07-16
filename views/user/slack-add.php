<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$title = Yii::t('app', 'Add Slack Integration');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <p>
    Slack連携を利用するには、あらかじめSlackのIncoming Webhookを設定する必要があります。
    （上級者向け）
  </p>
  <p>
    <a href="https://api.slack.com/incoming-webhooks" target="_blank">Incoming Webhookについて</a>,&#32;
    <a href="https://my.slack.com/services/new/incoming-webhook/" target="_blank">新しいWebhookの作成</a>
  </p>

  <?php $_ = ActiveForm::begin(['id' => 'add-form', 'action' => ['slack-add']]); echo "\n" ?>
    <?= $_->field($form, 'webhook_url')
      ->input('text', [
        'placeholder' => 'https://hooks.slack.com/services/AAAAAAAAA/BBBBBBBBB/CCCCCCCCCCCCCCCCCCCCCCCC',
      ])
      ->hint('DiscordのSlack互換エンドポイントURLも指定できます。') . "\n" ?>

    <?= $_->field($form, 'username')
      ->hint('省略するとWebhookに設定された名前が使用されます。') . "\n" ?>

    <?= $_->field($form, 'icon')
      ->input('text', [
        'placeholder' => ':emoji:'
      ])
      ->hint('<a href="http://www.emoji-cheat-sheet.com/" target="_blank">チートシート</a>。省略するとデフォルトのアイコンが使用されます。') . "\n" ?>

    <?= $_->field($form, 'channel')
      ->input('text', [
        'placeholder' => '#splatoon'
      ])
      ->hint('省略するとWebhookに設定されたChannelが使用されます。') . "\n" ?>

    <?= $_->field($form, 'language_id')
      ->dropDownList($languages)
      ->hint('ここで設定された言語で投稿されます。') . "\n" ?>

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
