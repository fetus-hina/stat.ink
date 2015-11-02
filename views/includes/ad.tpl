{{strip}}
  {{if $app->params.googleAdsense.client|default:'' != ''}}
    {{use class="jp3cki\yii2\googleadsense\GoogleAdSense" type="function"}}
    <div style="margin-bottom:15px">
      {{GoogleAdSense slot="{{$app->params.googleAdsense.slot|escape}}" client="{{$app->params.googleAdsense.client|escape}}" responsive=true}}
    </div>
  {{/if}}
{{/strip}}
