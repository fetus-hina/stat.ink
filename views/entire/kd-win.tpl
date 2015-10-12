{{strip}}
  {{set layout="main.tpl"}}
  <div class="container">
    <h1>
      {{$title = 'Winning Percentage based on Killed/Dead'|translate:'app'}}
      {{$title|escape}}
      {{set title="{{$app->name}} | {{$title}}"}}
    </h1>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

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
                  {{foreach range(0, 10) as $k}}
                    <th class="center kdcell">{{$k|escape}}</th>
                  {{/foreach}}
                  <th class="center kdcell">11+</th>
                </tr>
              </thead>
              <tbody>
                {{foreach range(0, 11) as $d}}
                  <tr>
                    <th class="center kdcell">
                      {{if $d === 11}}
                        11+
                      {{else}}
                        {{$d|escape}}
                      {{/if}}
                    </th>
                    {{foreach range(0, 11) as $k}}
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
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:15px">
        {{registerJsFile url="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js" async="async"}}
        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-0704984061430053" data-ad-slot="5800809033" data-ad-format="auto"></ins>
        {{registerJs}}(adsbygoogle = window.adsbygoogle || []).push({});{{/registerJs}}
      </div>
    </div>
  </div>
{{/strip}}
{{registerCss}}
.kdcell{width:{{(100/13)}}%!important}
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
      var battleCountCoefficient = Math.min(1.0, battle / (maxBattle * 0.75));
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
