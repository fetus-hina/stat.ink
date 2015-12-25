{{strip}}
  {{set layout="main.tpl"}}
  {{use class="yii\helpers\Url"}}

  {{$name = '{0}-san'|translate:'app':$user->name}}
  {{$title = "{0}'s Favorite Weapon"|translate:'app':$name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        <table class="table table-striped">
          <tbody>
            {{$total = 0}}
            {{foreach $list as $row}}
              {{$total = $total + $row->count}}
            {{/foreach}}

            {{$rank = 0}}
            {{$last = null}}
            {{foreach $list as $i => $row}}
              <tr class="weapon" data-name="{{$row->name|escape}}" data-count="{{$row->count|escape}}">
                <td class="text-right">
                  {{if $last !== $row->count}}
                    {{$rank = $i + 1}}
                    {{$last = $row->count}}
                  {{/if}}
                  {{$rank|escape}}
                </td>
                <td>
                  {{$row->name|escape}}
                </td>
                <td class="text-right">
                  {{$params = [
                      'nFormatted' => $row->count|number_format,
                      'n' => $row->count
                    ]}}
                  {{$link = [
                      'show/user',
                      'screen_name' => $user->screen_name,
                      'filter' => [
                        'weapon' => $row->key
                      ]
                    ]}}
                  <a href="{{Url::to($link)|escape}}">
                    {{'{nFormatted} {n, plural, =1{time} other{times}}'|translate:'app':$params}}
                  </a>
                </td>
                <td class="text-right">
                  {{($row->count*100/$total)|string_format:'%.2f%%'|escape}}
                </td>
              </tr>
            {{foreachelse}}
              <tr>
                <td>{{'There are no data.'|translate:'app'|escape}}</td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{include file="@app/views/includes/ad.tpl"}}
      </div>
    </div>
  </div>
{{/strip}}
