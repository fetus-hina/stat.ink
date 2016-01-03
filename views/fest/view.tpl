{{strip}}
  {{set layout="main.tpl"}}
  {{*
    {{$title = 'Splatfest'|translate:'app'}}
    {{set title="{{$app->name}} | {{$title}} - {{$fest->name}}"}}
  *}}
  {{$title = "フェス「{{$fest->name}}」の各チーム勝率"}}
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

    <p>
      {{$app->name|escape}}へ投稿されたデータからチームを推測し、勝率を計算したデータです。利用者の偏りから正確な勝率を表していません。
    </p>
    {{if $fest->region->key === 'jp'}}
      <p>
        <a href="https://fest.ink/{{$fest->order|escape}}">
          公式サイトから取得したデータを基に推測した勝率はイカフェスレートで確認できます。
        </a>
      </p>
    {{/if}}

    <h2 id="rate">
      推定勝率: <span class="total-rate" data-team="alpha">取得中</span> VS <span class="total-rate" data-team="bravo">取得中</span>
    </h2>
    <p>
      {{$alpha->name|escape}}チーム: <span class="total-rate" data-team="alpha">取得中</span>（サンプル数：<span class="sample-count" data-team="alpha">???</span>）
    </p>
    <div class="progress">
      <div class="progress-bar progress-bar-danger progress-bar-striped total-progressbar" style="width:0%" data-team="alpha">
      </div>
    </div>
    <p>
      {{$bravo->name|escape}}チーム: <span class="total-rate" data-team="bravo">取得中</span>（サンプル数：<span class="sample-count" data-team="bravo">???</span>）
    </p>
    <div class="progress">
      <div class="progress-bar progress-bar-success progress-bar-striped total-progressbar" style="width:0%" data-team="bravo">
      </div>
    </div>
  </div>
  {{if $alpha->color_hue !== null}}
    {{registerCss}}
      .progress-bar[data-team="alpha"] {
        background-color: hsl({{$alpha->color_hue|escape}},67%,48%);
      }
    {{/registerCss}}
  {{/if}}
  {{if $bravo->color_hue !== null}}
    {{registerCss}}
      .progress-bar[data-team="bravo"] {
        background-color: hsl({{$bravo->color_hue|escape}},67%,48%);
      }
    {{/registerCss}}
  {{/if}}
  {{registerJs position=POS_BEGIN}}
    window.fest = {
      start: new Date({{$fest->start_at|strtotime}} * 1000),
      end: new Date({{$fest->end_at|strtotime}} * 1000),
      data: {{$results|json_encode}}
    };
  {{/registerJs}}
  {{registerJs}}
    (function($, info) {
      var wins = {
        alpha: info.data.map(function(a){return a.alpha}).reduce(function(x,y){return x+y},0),
        bravo: info.data.map(function(a){return a.bravo}).reduce(function(x,y){return x+y},0),
        total: 0
      };
      wins.total = wins.alpha + wins.bravo;
      var wp = {
        alpha: wins.total > 0 ? wins.alpha * 100 / wins.total : Number.NaN,
        bravo: wins.total > 0 ? wins.bravo * 100 / wins.total : Number.NaN
      };

      $('.total-rate').each(function(){
        var v = wp[$(this).attr('data-team')];
        console.log(v);
        if (v === undefined || Number.isNaN(v)) {
          $(this).text('???');
        } else {
          $(this).text(v.toFixed(1) + '%');
        }
      });

      $('.sample-count').each(function(){
        var v = wins[$(this).attr('data-team')];
        if (v === undefined || Number.isNaN(v)) {
          $(this).text('???');
        } else {
          $(this).text(v);
        }
      });

      $('.total-progressbar').each(function(){
        var v = wp[$(this).attr('data-team')];
        if (v === undefined || Number.isNaN(v)) {
          $(this).css('width', 0);
        } else {
          $(this).css('width', v + '%');
        }
      });
    })(jQuery, window.fest);
  {{/registerJs}}
{{/strip}}
