{{strip}}
  {{if $app->params.googleAdsense.client|default:'' != ''}}
    {{use class="jp3cki\yii2\googleadsense\GoogleAdSense" type="function"}}
    {{GoogleAdSense slot="5800809033" client="ca-pub-0704984061430053" responsive=true}}
  {{/if}}
{{/strip}}
