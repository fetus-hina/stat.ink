{{strip}}
  {{set layout="main.tpl"}}
  {{$_map = $map->name|translate:'app-map'}}
  {{$title = 'Stages'|translate:'app'|cat:' - ':$_map}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  
  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    <p class="form-inline">
      <select class="form-control" id="change-map" disabled>
        {{use class="yii\helpers\Url"}}
        {{foreach $maps as $_k => $_v}}
          {{$_url = Url::to(['stage/map', 'map' => $_k])}}
          <option value="{{$_url|escape}}" {{if $map->key === $_k}}selected{{/if}}>
            {{$_v|escape}}
          </option>
        {{/foreach}}
      </select>
      {{registerJs}}
        (function($){
          "use strict";
          $(function(){
            $('#change-map')
              .change(function(){
                window.location.href=$(this).val()
              })
              .prop('disabled',!1)
          })
        })(jQuery);
      {{/registerJs}}
    </p>

    <p>
      {{foreach $data as $__rule}}
        {{if !$__rule@first}}&#32;|&#32;{{/if}}
        <a href="#{{$__rule->rule->key|escape}}">
          {{$__rule->rule->name|translate:'app-rule'|escape}}
        </a>
      {{/foreach}}
    </p>

    <div class="row">
      {{foreach $data as $__rule}}
        {{$_rule = $__rule->rule}}
        {{$_data = $__rule->history}}
        <div id="{{$_rule->key|escape}}" class="col-xs-12 col-sm-6">
          <h2>
            {{$_rule->name|translate:'app-rule'|escape}}
          </h2>
          <table class="table table-striped">
            <thead>
              <tr>
                <th colspan="3">{{'Period'|translate:'app'|escape}}</th>
                <th>{{'Interval'|translate:'app'|escape}}</th>
              </tr>
            </thead>
            <tbody>
              {{foreach $_data as $_h}}
                <tr>
                  <td class="text-right">{{$_h->start|as_datetime:'medium':'short'|escape}}</td>
                  <td class="range">-</td>
                  <td class="text-left">{{$_h->end|as_datetime:'medium':'short'|escape}}</td>
                  <td class="text-left">
                    {{if $_h->interval === null}}
                      <div class="text-center">
                        -
                      </div>
                    {{elseif $_h->interval === 0}}
                      {{'Continue'|translate:'app'|escape}}
                    {{else}}
                      {{$app->formatter->asDuration($_h->interval)|escape}}
                    {{/if}}
                  </td>
                </tr>
              {{/foreach}}
            </tbody>
          </table>
        </div>
      {{/foreach}}
    </div>
  </div>
  {{registerCss}}
    td.range{width:1em;text-align:center;padding-left:0;padding-right:0}
  {{/registerCss}}
{{/strip}}
