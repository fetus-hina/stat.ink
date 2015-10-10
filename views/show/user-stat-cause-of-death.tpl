{{strip}}
  {{set layout="main.tpl"}}
  {{\app\assets\FlotPieAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$name = '{0}-san'|translate:'app':$user->name}}
      {{$title = "{0}'s Battle Stat (Cause of Death)"|translate:'app':$name}}
      {{$title|escape}}
      {{set title="{{$app->name}} | {{$title}}"}}
    </h1>
    <p>
      ルール別などの詳細な表示は、ほかの統計とともに作成中です
    </p>

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
              <tr class="cause-of-death" data-name="{{$row->name|escape}}" data-count="{{$row->count|escape}}">
                <td class="right">
                  {{if $last !== $row->count}}
                    {{$rank = $i + 1}}
                    {{$last = $row->count}}
                  {{/if}}
                  {{$rank|escape}}
                </td>
                <td>
                  {{$row->name|escape}}
                </td>
                <td class="right">
                  {{$params = [
                      'nFormatted' => $row->count|number_format,
                      'n' => $row->count
                    ]}}
                  {{'{nFormatted} {n, plural, =1{time} other{times}}'|translate:'app':$params}}
                </td>
                <td class="right">
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
(function($){
  "use strict";
  var data = [];
  var total_count = 0;
  var others = 0;
  $('tr.cause-of-death').each(function() {
    total_count += ~~$(this).attr('data-count');
  }
  $('tr.cause-of-death').each(function() {
    var $this = $(this);
    data.push({
      'label': $this.attr('data-name'),
      'data': ~~$this.attr('data-count')
    });
  });

  $('.pie-flot-container').each(function () {
    var $container = $(this);
    $.plot($container, data, {
      series: {
        pie: {
          show: true,
          radius: 1,
          label: {
            show: false,
            radius: .618,
            formatter: function(label, slice) {
              return $('<div>').append(
                $('<div>').css({
                  'fontSize': '1em',
                  'lineHeight': '1.1em',
                  'textAlign': 'center',
                  'padding': '2px',
                  'color': '#fff',
                  'textShadow': '0px 0px 3px #000',
                }).append(
                  label
                ).append( 
                  $('<br>')
                ).append(
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
      }
    });
  });
})(jQuery);
{{/literal}}{{/registerJs}}
