{{strip}}
  {{set layout="main.tpl"}}

  {{$title = 'Winning Percentage based on K/D'|translate:'app'}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>
    <p>
      {{'This website has implemented support for color-blindness. Please check "Color-Blind Support" in the "User Name/Guest" menu of the navbar to enable it.'|translate:'app'|escape}}
    </p>

    {{AdWidget}}
    {{SnsWidget}}

    {{use class="yii\bootstrap\ActiveForm" type="block"}}
    {{ActiveForm assign="_" id="filter-form" action=['entire/kd-win'] method="get" layout="inline"}}
      {{$_->field($filter, 'map')->dropDownList($maps)->label(false)}}
      &#32;
      {{$_->field($filter, 'weapon')->dropDownList($weapons)->label(false)}}
      &#32;
      <input type="submit" value="{{'Summarize'|translate:'app'|escape}}" class="btn btn-primary">
    {{/ActiveForm}}

    <h3>{{'Legend'|translate:'app'|escape}}</h3>
    <div class="table-responsive" style="max-width:8em;margin-right:2em;float:left">
      <table class="table table-bordered table-condensed rule-table">
        <tbody>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="1" data-percent="90">
            <td class="text-center kdcell">90%</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="1" data-percent="{{(10+(90-10)*5/6)}}">
            <td class="text-center kdcell">:</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="1" data-percent="{{(10+(90-10)*4/6)}}">
            <td class="text-center kdcell">:</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="1" data-percent="{{(10+(90-10)*3/6)}}">
            <td class="text-center kdcell">50%</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="1" data-percent="{{(10+(90-10)*2/6)}}">
            <td class="text-center kdcell">:</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="1" data-percent="{{(10+(90-10)*1/6)}}">
            <td class="text-center kdcell">:</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="1" data-percent="10">
            <td class="text-center kdcell">10%</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="table-responsive" style="max-width:8em;margin-right:2em;float:left">
      <table class="table table-bordered table-condensed rule-table">
        <tbody>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="100" data-percent="100">
            <td class="text-center kdcell">{{'Many'|translate:'app'|escape}}</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="42" data-percent="100">
            <td class="text-center kdcell">:</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="33" data-percent="100">
            <td class="text-center kdcell">:</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="25" data-percent="100">
            <td class="text-center kdcell">:</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="17" data-percent="100">
            <td class="text-center kdcell">:</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="8" data-percent="100">
            <td class="text-center kdcell">:</td>
          </tr>
          <tr>
            <td class="text-center kdcell percent-cell" data-battle="0" data-percent="100">
            <td class="text-center kdcell">{{'Few'|translate:'app'|escape}}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div style="clear:left"></div>

    {{foreach $rules as $rule}}
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <h2 id="{{$rule->key|escape}}">{{$rule->name|escape}}</h2>
          <div class="table-responsive table-responsive-force">
            <table class="table table-bordered table-condensed rule-table">
              <thead>
                <tr>
                  <th class="text-center kdcell">
                    {{'d'|translate:'app'|escape}}＼{{'k'|translate:'app'|escape}}
                  </th>
                  {{foreach range(0, 15) as $k}}
                    <th class="text-center kdcell">{{$k|escape}}</th>
                  {{/foreach}}
                  <th class="text-center kdcell">16+</th>
                </tr>
              </thead>
              <tbody>
                {{foreach range(0, 16) as $d}}
                  <tr>
                    <th class="text-center kdcell">
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
                      <td class="text-center kdcell percent-cell" data-battle="{{$data->battle|escape}}" data-percent="{{$percent|escape}}">
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
  /*
    var colorHigh = $.Color("#3e8ffa"); // H:214, S:75, V:98 / S:95, L:61
    var colorMid  = $.Color("#888888"); // H:  0, S: 0, V:53 / S: 0, L:53
    var colorLow  = $.Color("#fa833e"); // H: 22, S:75, V:98 / S:95, L:61
  */

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
      var battleCountCoefficient = Math.min(1.0, (battle * 2) / maxBattle);
      var percent = parseFloat($cell.attr('data-percent'));

      /* 10%-90% scale to 0%-100% */
      var ratio = Math.min(100, Math.max(0, (percent - 10) * (100 / 80)));

      /* calc background color */
      if (window.colorLock) {
        if (ratio >= 50) {
          $cell.css(
            'background-color',
            $.Color({
              hue: 214,
              saturation: 0.95 * ((ratio - 50) * 2 / 100),
              lightness: 0.53 + 0.08 * ((ratio - 50) * 2 / 100),
            })
            .alpha(battleCountCoefficient)
            .blend($.Color("#ffffff"))
            .toRgbaString()
          );
        } else {
          $cell.css(
            'background-color',
            $.Color({
              hue: 22,
              saturation: 0.95 * ((50 - ratio) * 2 / 100),
              lightness: 0.53 + 0.08 * ((50 - ratio) * 2 / 100)
            })
            .alpha(battleCountCoefficient)
            .blend($.Color("#ffffff"))
            .toRgbaString()
          );
        }
      } else {
        $cell.css(
          'background-color',
          $.Color({
            hue: 120 * ratio / 100,
            saturation: 0.95,
            lightness: 0.53
          })
          .alpha(battleCountCoefficient)
          .blend($.Color("#ffffff"))
          .toRgbaString()
        );
      }

      var c = $.Color($cell.css('background-color'));
      var y = Math.round(c.red() * 0.299 + c.green() * 0.587 + c.blue() * 0.114);
      $cell.css('color', (y > 153) ? '#000' : '#fff');
    });
  });
})(jQuery);
{{/literal}}{{/registerJs}}
