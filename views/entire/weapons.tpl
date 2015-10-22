{{strip}}
  {{set layout="main.tpl"}}
  <div class="container">
    <h1>
      {{$title = 'Weapons'|translate:'app'}}
      {{$title|escape}}
      {{set title="{{$app->name}} | {{$title}}"}}
    </h1>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    <h2>
      {{'Favorite Weapons'|translate:'app'|escape}}
    </h2>
    <table class="table table-striped table-condensed">
      <tbody>
        {{$_max = 0}}
        {{foreach $weapons as $_w}}
          <tr>
            <td class="text-right" style="width:15em">
              {{$_w->weapon->name|default:'?'|translate:'app-weapon'|escape}}
            </td>
            <td>
              {{if $_max < $_w->user_count}}
                {{$_max = $_w->user_count}}
              {{/if}}
              {{if $_max > 0}}
                <div class="progress">
                  <div class="progress-bar" role="progressbar" style="width:{{($_w->user_count*100/$_max)|escape}}%;">
                    {{$_w->user_count|number_format|escape}}
                  </div>
                </div>
              {{/if}}
            </td>
          </tr>
        {{/foreach}}
      </tbody>
    </table>
  </div>
{{/strip}}
