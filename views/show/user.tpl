{{strip}}
  {{set layout="main.tpl"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  {{use class="yii\widgets\ListView"}}
  {{\app\assets\TinyColorAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$name = '{0}-san'|translate:'app':$user->name}}
      {{$title = "{0}'s Log"|translate:'app':$name}}
      {{$title|escape}}
      {{set title="{{$app->name}} | {{$title}}"}}
    </h1>
    
    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

{{*
    <h2>
      {{'Recent Results'|translate:'app'|escape}}
    </h2>
    <p>
      Coming soon
    </p>
*}}

    <h2>
      {{'Battles'|translate:'app'|escape}}
    </h2>
    <div class="row">
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 pull-right" style="padding:15px">
        <div style="border:1px solid #ccc;border-radius:5px;padding:15px">
          {{ActiveForm assign="_" id="filter-form" action=['show/user', 'screen_name' => $user->screen_name] method="get"}}
            {{$_->field($filter, 'rule')->dropDownList($rules)->label(false)}}
            {{$_->field($filter, 'map')->dropDownList($maps)->label(false)}}
            {{$_->field($filter, 'weapon')->dropDownList($weapons)->label(false)}}
            {{$_->field($filter, 'result')->dropDownList($results)->label(false)}}
{{*
            TODO:k/d<br>
            TODO:期間<br>
*}}
            <input type="submit" value="{{'Search'|translate:'app'|escape}}" class="btn btn-primary">
          {{/ActiveForm}}
        </div>
      </div>
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9" id="battles">
        {{ListView::widget([
            'dataProvider' => $battleDataProvider,
            'itemView' => 'battle.tablerow.tpl',
            'itemOptions' => [ 'tag' => false ],
            'layout' => '{summary}{pager}'
          ])}}
        <table class="table table-striped">
          <thead>
            <tr>
              <th></th>
              <th>{{'Rule'|translate:'app'|escape}}</th>
              <th>{{'Map'|translate:'app'|escape}}</th>
              <th>{{'Weapon'|translate:'app'|escape}}</th>
              <th>{{'Result'|translate:'app'|escape}}</th>
              <th>{{'k'|translate:'app'|escape}}/{{'d'|translate:'app'|escape}}</th>
              <th class="visible-lg">{{'Kill Ratio'|translate:'app'|escape}}</th>
              <th>{{'Date Time'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{ListView::widget([
              'dataProvider' => $battleDataProvider,
              'itemView' => 'battle.tablerow.tpl',
              'itemOptions' => [ 'tag' => false ],
              'layout' => '{items}'
            ])}}
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 pull-right">
        {{include "user-miniinfo.tpl" user=$user}}
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 pull-right" style="margin-top:15px">
        {{registerJsFile url="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js" async="async"}}
        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-0704984061430053" data-ad-slot="5800809033" data-ad-format="auto"></ins>
        {{registerJs}}(adsbygoogle = window.adsbygoogle || []).push({});{{/registerJs}}
      </div>
    </div>
  </div>
{{/strip}}
{{registerJs}}{{literal}}
(function(){
  "use strict";
  var lastPeriodId = null;
  $('.battle-row').each(function(){
    var $row = $(this);
    if ($row.attr('data-period') === lastPeriodId) {
      return;
    }
    if (lastPeriodId !== null) {
      $row.css('border-top', '2px solid grey');
    }
    lastPeriodId = $row.attr('data-period');
  });

  var hsv2rgb = function (h, s, v) {
    while (h < 0) {
      h += 360;
    }
    h = h % 360;
    return tinycolor.fromRatio({h: h / 360.0, s: s, v: v}).toHexString();
  };

  var calcColor = function (ratio) {
    var redH = 0, greenH = 120, defaultH = 60;
    var S = 0.40, V = 0.90;
    var H = Math.round((function () {
      if (ratio == 1.0) {
        return defaultH;
      } else if (ratio >= 3.0) {
        return greenH;
      } else if (ratio <= 1/3) {
        return redH;
      } else if (ratio > 1.0) {
        var pos = (ratio - 1.0) / 2.0;
        return defaultH + (greenH - defaultH) * pos;
      } else {
        var pos = (ratio - 1/3) * (3/2);
        return redH + (defaultH - redH) * pos;
      }
    })());
    return hsv2rgb(H, S, V);
  };

  $('.kill-ratio').each(function() {
    var $this = $(this);
    $this.css('background-color', calcColor(parseFloat($this.attr('data-kill-ratio'))));
  });
})();
{{/literal}}{{/registerJs}}{{registerCss}}{{literal}}
.nobr{white-space:nowrap}
{{/literal}}{{/registerCss}}
