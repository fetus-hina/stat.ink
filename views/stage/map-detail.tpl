{{strip}}
  {{set layout="main.tpl"}}
  {{$_map = $map->name|translate:'app-map'}}
  {{$_rule = $rule->name|translate:'app-rule'}}
  {{$title = 'Stages'|translate:'app'|cat:' - ':$_map:' - ':$_rule}}
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
      {{use class="yii\helpers\Url"}}
      <select class="form-control change-map" disabled>
        {{foreach $maps as $_k => $_v}}
          {{$_url = Url::to(['stage/map-detail', 'map' => $_k, 'rule' => $rule->key])}}
          <option value="{{$_url|escape}}" {{if $map->key === $_k}}selected{{/if}}>
            {{$_v|escape}}
          </option>
        {{/foreach}}
      </select>
      <select class="form-control change-map" disabled>
        {{foreach $rules as $_k => $_v}}
          {{$_url = Url::to(['stage/map-detail', 'map' => $map->key, 'rule' => $_k])}}
          <option value="{{$_url|escape}}" {{if $rule->key === $_k}}selected{{/if}}>
            {{$_v|escape}}
          </option>
        {{/foreach}}
      </select>
      {{registerJs}}
        (function($){
          "use strict";
          $(function(){
            $('.change-map')
              .change(function(){
                window.location.href=$(this).val()
              })
              .prop('disabled',!1)
          })
        })(jQuery);
      {{/registerJs}}
    </p>

    <h2 id="calendar">
      {{'Calendar'|translate:'app'|escape}}
      &#32;
      <a href="#history" class="btn btn-default btn-sm">
        {{'Session History'|translate:'app'|escape}}
      </a>
    </h2>
    {{\app\assets\SessionCalendarAsset::register($this)|@void}}
    <p>
      <button id="cal-prev" class="btn btn-default btn-xs">
        <span class="fa fa-chevron-left"></span>
      </button>
      &#32;
      <button id="cal-next" class="btn btn-default btn-xs">
        <span class="fa fa-chevron-right"></span>
      </button>
    </p>
    <div class="calendar" data-url="{{url route='/stage/map-history-json' map=$map->key rule=$rule->key}}" data-next="#cal-next" data-prev="#cal-prev">
    </div>

    <div class="row">
      <div class="col-xs-12 col-md-6">
        <h2 id="weapons">
          {{'Weapon Trends'|translate:'app'|escape}}
        </h2>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>{{'Weapon'|translate:'app'|escape}}
                <th>{{'Recent Use %'|translate:'app'|escape}}
              </tr>
            </thead>
            <tbody>
              {{$_total = 0}}
              {{$_max = 0}}
              {{foreach $weapons as $_}}
                {{$_max = max($_max, $_->battles)}}
                {{$_total = $_total + $_->battles}}
              {{/foreach}}
              {{foreach $weapons as $_}}
                <tr>
                  <td>
                    {{$_->weapon->name|translate:'app-weapon'|escape}}
                  </td>
                  <td>
                    {{if $_max > 0 && $_total > 0}}
                      <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width:{{($_->battles*100/$_max)|escape}}%">
                          {{($_->battles*100/$_total)|number_format:2|escape}} %
                        </div>
                      </div>
                    {{/if}}
                  </td>
                </tr>
              {{/foreach}}
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-xs-12 col-md-6">
        <h2 id="history">
          {{'Session History'|translate:'app'|escape}}
        </h2>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th></th>
                <th colspan="3" class="text-center">{{'Period'|translate:'app'|escape}}</th>
                <th class="text-center">{{'Interval'|translate:'app'|escape}}</th>
              </tr>
            </thead>
            <tbody>
              {{foreach $history as $_h}}
                <tr>
                  <td class="text-center">
                    {{if $_h->start > time()}}
                      {{'Scheduled'|translate:'app'|escape}}
                    {{elseif $_h->end > time()}}
                      {{'In session'|translate:'app'|escape}}
                    {{else}}
                      {{$_h->end|relative_time|escape}}
                    {{/if}}
                  </td>
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
      </div>
    </div>
  </div>

  {{registerCss}}
    td.range{width:1em;text-align:center;padding-left:0;padding-right:0}
    .progress{margin-bottom:0;min-width:150px}
  {{/registerCss}}
{{/strip}}
