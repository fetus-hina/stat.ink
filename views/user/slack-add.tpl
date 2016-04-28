{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Add Slack Integration'|translate:'app'}}"}}
  {{use class="yii\helpers\ArrayHelper"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <h1>
      {{'Add Slack Integration'|translate:'app'|escape}}
    </h1>

    <p>
      Slack連携を利用するには、あらかじめSlackのIncoming Webhookを設定する必要があります。
      （上級者向け）
    </p>
    <p>
      <a href="https://api.slack.com/incoming-webhooks" target="_blank">Incoming Webhookについて</a>,&#32;
      <a href="https://my.slack.com/services/new/incoming-webhook/" target="_blank">新しいWebhookの作成</a>
    </p>
    
    {{ActiveForm assign="_" id="add-form" action=['user/slack-add']}}
      {{$_->field($form, 'webhook_url')
          ->input('text', [
              'placeholder' => 'https://hooks.slack.com/services/AAAAAAAAA/BBBBBBBBB/CCCCCCCCCCCCCCCCCCCCCCCC'
            ])
        }}

      {{$_->field($form, 'username')
          ->hint('省略するとWebhookに設定された名前が使用されます。')
        }}

      {{$_->field($form, 'icon')
          ->input('text', [
              'placeholder' => ':emoji:'
            ])
          ->hint('<a href="http://www.emoji-cheat-sheet.com/" target="_blank">チートシート</a>。省略するとデフォルトのアイコンが使用されます。')
        }}

      {{$_->field($form, 'channel')
          ->input('text', [
              'placeholder' => '#splatoon'
            ])
          ->hint('省略するとWebhookに設定されたChannelが使用されます。')
        }}

      {{$_->field($form, 'language_id')
          ->dropDownList($languages)
          ->hint('ここで設定された言語で投稿されます。')
        }}

      {{Html::submitButton(
          Yii::t('app', 'Add'),
          ['class' => 'btn btn-lg btn-primary btn-block']
        )}}
    {{/ActiveForm}}

    <div style="margin-top:15px">
      {{Html::a(
          Yii::t('app', 'Back'),
          ['user/profile'],
          ['class' => 'btn btn-lg btn-default btn-block']
        )}}
    </div>
  </div>
{{/strip}}
