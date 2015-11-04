{{strip}}
  {{set layout="main.tpl"}}

  {{$title = 'Winning Percentage based on Killed/Dead'|translate:'app'}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        {{include file="@app/views/includes/ad.tpl"}}
      </div>
    </div>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    {{use class="yii\bootstrap\ActiveForm" type="block"}}
    {{ActiveForm assign="_" id="filter-form" action=['entire/kd-win'] method="get" layout="inline"}}
      {{$_->field($filter, 'map')->dropDownList($maps)->label(false)}}
      &#32;
      {{$_->field($filter, 'weapon')->dropDownList($weapons)->label(false)}}
      &#32;
      <input type="submit" value="{{'Summarize'|translate:'app'|escape}}" class="btn btn-primary">
    {{/ActiveForm}}

    {{foreach $rules as $rule}}
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <h2 id="{{$rule->key|escape}}">{{$rule->name|escape}}</h2>
          <div class="table-responsive table-responsive-force">
            <table class="table table-bordered table-condensed rule-table">
              <thead>
                <tr>
                  <th class="center kdcell">
                    {{'d'|translate:'app'|escape}}＼{{'k'|translate:'app'|escape}}
                  </th>
                  {{foreach range(0, 15) as $k}}
                    <th class="center kdcell">{{$k|escape}}</th>
                  {{/foreach}}
                  <th class="center kdcell">16+</th>
                </tr>
              </thead>
              <tbody>
                {{foreach range(0, 16) as $d}}
                  <tr>
                    <th class="center kdcell">
                      {{if $d === 16}}
                        16+
                      {{else}}
                        {{$d|escape}}
                      {{/if}}
                    </th>
                    {{foreach range(0, 16) as $k}}
                      {{$data = $rule->data[$k][$d]}}
                      {{$percent = null}}
                      {{if $data->battle > 0}}
                        {{$percent = $data->win * 100 / $data->battle}}
                      {{/if}}
                      <td class="center kdcell percent-cell" data-battle="{{$data->battle|escape}}" data-percent="{{$percent|escape}}">
                        {{$data->win|escape}} / {{$data->battle|escape}}<br>
                        {{if $percent === null}}
                          -
                        {{else}}
                          {{$percent|string_format:'%.1f%%'|escape}}
                        {{/if}}
                      </td>
                    {{/foreach}}
                  </tr>
                {{/foreach}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    {{/foreach}}
  </div>
{{/strip}}
{{registerCss}}
.kdcell{width:{{(100/(16+2))}}%!important}
.center{text-align:center!important}
{{/registerCss}}
{{registerJs}}{{literal}}
(function($){
  $('.rule-table').each(function() {
    var $table = $(this);
    var $cells = $('.percent-cell', $table);
    var maxBattle = 0;
    $cells.each(function() {
      var value = ~~$(this).attr('data-battle');
      if (maxBattle < value) {
        maxBattle = value;
      }
    });
    $cells.each(function() {
      var $cell = $(this);
      var battle = ~~$cell.attr('data-battle');
      if (battle < 1) {
        return;
      }
      var battleCountCoefficient = Math.min(1.0, battle / (maxBattle * 0.5));
      var percent = parseFloat($cell.attr('data-percent'));
      var h = 120 * (percent / 100); // 0%: 0, 100%: 120
      var s = 0.85;
      var l = 1.0 - 0.5 * battleCountCoefficient; // 0:1.0 max:0.5
      var hsl = 'hsl(' + h + ', ' + (s * 100) + '%' + ', ' + (l * 100) + '%)';
      $cell.css('background-color', hsl);
    });
  });
})(jQuery);
{{/literal}}{{/registerJs}}
