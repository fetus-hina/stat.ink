{{strip}}
{{\app\assets\AppAsset::register($this)|@void}}
{{\app\assets\GithubForkRibbonJsAsset::register($this)|@void}}
{{$this->beginPage()|@void}}
  <!DOCTYPE html>
  <html lang="{{$app->language|escape}}">
    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
      <meta name="apple-mobile-web-app-capable" content="yes">
      <meta name="format-detection" content="telephone=no,email=no,address=no">
      {{\yii\helpers\Html::csrfMetaTags()}}
      <title>{{$this->title|default:$app->name|default:'stat.ink'|escape}}</title>
      {{\app\components\helpers\I18n::languageLinkTags()}}
      {{$this->head()}}
    </head>
    <body>
      {{$this->beginBody()|@void}}
        {{include '@app/views/layouts/navbar.tpl'}}
        {{$content}}
        {{include '@app/views/layouts/footer.tpl'}}
        {{if !$app->user->isGuest}}
          {{include '@app/views/includes/battle-input-modal.tpl'}}
        {{/if}}
        <span id="event"></span>
        {{if $app->params.googleAnalytics != ''}}
          {{use class="\cybercog\yii\googleanalytics\widgets\GATracking" type="function"}}
          {{GATracking trackingId=$app->params.googleAnalytics}}
        {{/if}}
        {{$_flashes = $app->getSession()->getAllFlashes()}}
        {{if $_flashes}}
          {{\app\assets\BootstrapNotifyAsset::register($this)|@void}}
          {{foreach $_flashes as $_key => $_messages}}
            {{if $_messages|@is_array}}
              {{foreach $_messages as $_message}}
                {{registerJs}}
                  jQuery.notify({
                    message:"{{$_message|escape|escape:javascript}}",
                    type:"{{$_key|escape:javascript}}"
                  });
                {{/registerJs}}
              {{/foreach}}
            {{else}}
              {{registerJs}}
                jQuery.notify({
                  message:"{{$_messages|escape|escape:javascript}}",
                },{
                  type:"{{$_key|escape:javascript}}",
                  z_index:11031
                });
              {{/registerJs}}
            {{/if}}
          {{/foreach}}
        {{/if}}
      {{$this->endBody()|@void}}
    </body>
  </html>
{{$this->endPage()|@void}}
{{/strip}}
