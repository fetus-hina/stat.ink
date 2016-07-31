{{strip}}
  {{set layout="main.tpl"}}

  {{$title = 'Downloads'|translate:'app'}}
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
      データファイルをその場で生成してからダウンロード処理が行われます。
      クリックしてからしばらく時間がかかりますが、連打しないでください。
    </p>

    <p>
      各言語や文字コードは、ブキやステージの名前のローカライズ部分にのみ影響します。（どれを落としても本質的な情報は同じです）
    </p>

    <p>
      ダウンロード後すぐに何かわかるデータではありません。
      表計算ソフト(Excel等)やプログラムを駆使して何かを解析することを前提としたデータです。
    </p>

    <ul>
      <li>
        <span class="fa fa-file-excel-o"></span> ブキ・ルール・ステージ別にバトル数・勝率を集計したもの (CSV)
        {{include file="dl-langs.inc.tpl" route="download-stats/weapon-rule-map"}}
      </li>
    </ul>
  </div>
{{/strip}}
