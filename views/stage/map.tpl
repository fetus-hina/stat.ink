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
            <a href="{{url route="/stage/map-detail" rule=$_rule->key map=$map->key}}">
              {{$_rule->name|translate:'app-rule'|escape}}
            </a>
          </h2>
          
          {{if $__rule->trends}}
            {{use class="app\assets\BukiiconsAsset"}}
            {{$_asset = BukiiconsAsset::register($this)}}
            {{$_am = $app->getAssetManager()}}
            {{registerCss}}
              .trends ul {
                list-style-type: none;
                display: block;
                overflow: hidden;
                margin: 0;
                padding: 0;
              }

              .trends ul li {
                display: inline-block;
                width: 20%;
                margin: 0;
                padding: 0;
              }

              .trends img {
                width: 100%;
                height: auto;
              }
            {{/registerCss}}
            <h3>
              {{'Trends'|translate:'app'|escape}}
            </h3>
            <div class="trends">
              <ul>
                {{foreach $__rule->trends as $_}}
                  <li>
                    {{$_weapon = $_->weapon}}
                    {{$_url = $_am->getAssetUrl($_asset, $_weapon->key|cat:'.png')}}
                    {{$_pct = ($_->battles * 100 / $__rule->trendTotalBattles)|number_format:2}}
                    {{$_name = $_weapon->name|translate:'app-weapon'}}
                    {{$_title = '%s / %s%%'|sprintf:$_name:$_pct}}
                    <img src="{{$_url|escape}}" class="auto-tooltip" alt="{{$_name|escape}}" title="{{$_title|escape}}">
                  </li>
                {{/foreach}}
              </ul>
              <p class="text-right">
                <a href="{{url route="/stage/map-detail" rule=$_rule->key map=$map->key}}#weapons">
                  {{'Details'|translate:'app'|escape}}
                </a>
              </p>
            </div>
          {{/if}}

          <h3>
            {{'History'|translate:'app'|escape}}
          </h3>
          <table class="table table-striped">
            <thead>
              <tr>
                <th></th>
                <th colspan="3" class="text-center">{{'Period'|translate:'app'|escape}}</th>
                <th class="text-center">{{'Interval'|translate:'app'|escape}}</th>
              </tr>
            </thead>
            <tbody>
              {{foreach $_data as $_h}}
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
              {{if count($_data) >= 5}}
                <tr>
                  <td class="text-right" colspan="5">
                    <a href="{{url route="/stage/map-detail" rule=$_rule->key map=$map->key}}#history">
                      {{'more...'|translate:'app'|escape}}
                    </a>
                  </td>
                </tr>
              {{/if}}
            </tbody>
          </table>
        </div>
      {{/foreach}}
    </div>
    <p class="text-right">
      <a href="http://graystar0907.wixsite.com/bukiicons" rel="external">
        {{"Weapons' icon were created by {0}."|translate:'app':'Stylecase'|escape}}
      </a>
    </p>
  </div>
  {{registerCss}}
    td.range{width:1em;text-align:center;padding-left:0;padding-right:0}
  {{/registerCss}}
{{/strip}}
