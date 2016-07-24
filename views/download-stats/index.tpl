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
      ダウンロードされるCSVの文字コードは、今のところ、<strong>なんと</strong>CP932固定です。
      スペイン語でダウンロードすると盛大に化けます。
    </p>

    {{use class="app\models\Language"}}
    {{$langs = Language::find()->orderBy('name ASC')->asArray()->all()}}
    {{\hiqdev\assets\flagiconcss\FlagIconCssAsset::register($this)|@void}}
    <ul>
      <li>
        <span class="fa fa-file-excel-o"></span> ブキ・ルール・ステージ別にバトル数・勝率を集計したもの (CSV)
        <ul>
          {{foreach $langs as $lang}}
            <li>
              <a href="{{url route="download-stats/weapon-rule-map" lang=$lang.lang}}">
                <span class="flag-icon flag-icon-{{$lang.lang|substr:3:2|strtolower|escape}}"></span>&#32;
                {{$lang.name|escape}}
              </a>
            </li>
          {{/foreach}}
        </ul>
      </li>
    </ul>
  </div>
{{/strip}}
