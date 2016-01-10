{{strip}}
  {{set layout="main.tpl"}}
  {{$title = 'Knockout Ratio'|translate:'app'}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}

  {{\jp3cki\yii2\flot\FlotPieAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    <div class="table-responsive table-responsive-force">
      <table class="table table-condensed graph-container">
        <thead>
          <tr>
            {{$width = 100 / ($rules|count + 1)}}
            <th style="width:{{$width|escape}}%;min-width:200px"></th>
            {{foreach $rules as $ruleKey => $ruleName}}
              <th style="width:{{$width|escape}}%;min-width:200px">
                {{$ruleName|escape}}
              </th>
            {{/foreach}}
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>
              <div style="display:inline-block;border:2px solid #ddd;padding:2px 5px">
                <div style="display:inline-block">
                  <span style="display:inline-block;width:1.618em;height:1em;line-height:1px" class="legend-bg" data-color="ko"></span>
                  &#32;
                  {{'Knockout'|translate:'app'|escape}}
                </div><br>
                <div style="display:inline-block">
                  <span style="display:inline-block;width:1.618em;height:1em;line-height:1px" class="legend-bg" data-color="time"></span>
                  &#32;
                  {{'Time is up'|translate:'app'|escape}}
                </div>
                {{registerJs}}
                  $('.legend-bg').each(function(){
                    $(this).css('background-color', window.colorScheme[$(this).attr('data-color')]);
                  });
                {{/registerJs}}
              </div>
            </th>
            {{foreach $rules as $ruleKey => $ruleName}}
              {{$totalBattleCount = 0}}
              {{$totalKOCount = 0}}
              {{foreach $maps as $mapKey => $mapName}}
                {{$_ = $data[$mapKey][$ruleKey]}}
                {{$totalBattleCount = $totalBattleCount + $_->battle_count|default:0}}
                {{$totalKOCount = $totalKOCount + $_->ko_count|default:0}}
              {{/foreach}}
              <td>
                {{if $totalBattleCount > 0}}
                  {{$tmp = ['battle' => $totalBattleCount, 'ko' => $totalKOCount]}}
                  <div class="pie-flot-container" data-json="{{$tmp|json_encode|escape}}">
                  </div>
                {{/if}}
              </td>
            {{/foreach}}
          </tr>
          {{\app\assets\MapImageAsset::register($this)|@void}}
          {{registerCss}}
            img.map-image {
              max-width: 15em;
              height: auto;
            }
          {{/registerCss}}
          {{foreach $maps as $mapKey => $mapName}}
            <tr>
              <th>
                {{$mapName.1|escape}}<br>
                {{$mapFile = 'daytime/'|cat:$mapName.0:'.jpg'}}
                <img src="{{$app->assetManager->getAssetUrl(
                    $app->assetManager->getBundle('app\assets\MapImageAsset'),
                    $mapFile
                  )}}" class="map-image">
              </th>
              {{foreach $rules as $ruleKey => $ruleName}}
                <td>
                  {{$_ = $data[$mapKey][$ruleKey]}}
                  {{if $_->battle_count|default:0 > 0}}
                    {{$tmp = ['battle' => $_->battle_count, 'ko' => $_->ko_count]}}
                    <div class="pie-flot-container" data-json="{{$tmp|json_encode|escape}}">
                    </div>
                  {{/if}}
                </td>
              {{/foreach}}
            </tr>
          {{/foreach}}
        </tbody>
      </table>
    </div>
  </div>
{{/strip}}
{{registerCss}}.pie-flot-container{height:200px}.pie-flot-container .error{display:none}{{/registerCss}}
{{registerJs}}
(function($) {
  var redrawFlot = function () {
    $('.pie-flot-container').each(function () {
      var $container = $(this);
      var data = JSON.parse($container.attr('data-flot'));
      if (data) {
        $.plot($container, data, {
          series: {
            pie: {
              show: true,
              radius: 1,
              label: {
                show: "auto",
                radius: .618,
                formatter: function(label, slice) {
                  return $('<div>').append(
                    $('<div>').css({
                      'fontSize': '0.8em',
                      'lineHeight': '1.1em',
                      'textAlign': 'center',
                      'padding': '2px',
                      'color': '#000',
                      'textShadow': '0px 0px 3px #fff',
                    }).append(
                      slice.data[0][1] + ' / ' +
                      Math.round(slice.data[0][1] / (slice.percent / 100)) // FIXME
                    ).append(
                      $('<br>')
                    ).append(
                      slice.percent.toFixed(1) + '%'
                    )
                  ).html();
                },
              },
            },
          },
          legend: {
            show: false
          },
          colors: [
            window.colorScheme.ko,
            window.colorScheme.time
          ]
        });
      }
    });
  };

  $('.pie-flot-container').each(function () {
    var $elem = $(this);
    var json = JSON.parse($elem.attr('data-json'));
    if (json.ok < 1 && json.battle < 1) {
      $elem.attr('data-flot', 'false');
    } else {
      var data = [
        {
          label: "KO",
          data: json.ko
        },
        {
          label: "Time Up",
          data: json.battle - json.ko
        }
      ];
      $elem.attr('data-flot', JSON.stringify(data));
    }
  });
  window.setTimeout(function () { redrawFlot(); }, 1);

  var timerId = null;
  var onResize = function () {
    var $elem = $('.pie-flot-container');
    if ($elem.length) {
      $elem.height(Math.min($elem.width(), 200));
    }
  };
  window.setTimeout(onResize, 1);
  $(window).resize(function () {
    if (timerId) {
      window.clearTimeout(timerId);
    }
    window.setTimeout(function () {
      timerId = null;
      onResize();
    }, 33);
  });
})(jQuery);
{{/registerJs}}
