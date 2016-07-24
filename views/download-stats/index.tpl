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

    {{use class="app\models\Language"}}
    {{$langs = Language::find()
        ->with('languageCharsets')
        ->orderBy('{{language}}.[[name]] ASC')
        ->asArray()
        ->all()}}
    {{\hiqdev\assets\flagiconcss\FlagIconCssAsset::register($this)|@void}}
    <ul>
      <li>
        <span class="fa fa-file-excel-o"></span> ブキ・ルール・ステージ別にバトル数・勝率を集計したもの (CSV)
        <table class="dl-langs">
          <tbody>
            {{foreach $langs as $lang}}
              <tr>
                <th>
                  <span class="flag-icon flag-icon-{{$lang.lang|substr:3:2|strtolower|escape}}"></span>&#32;
                  {{$lang.name|escape}}
                </th>
                {{foreach $lang.languageCharsets as $_charset}}
                  {{$charset = $_charset.charset}}
                  <td>
                    <a href="{{url route="download-stats/weapon-rule-map" lang=$lang.lang charset=$charset.php_name}}">
                      {{if $_charset.is_win_acp}}
                        <span class="fa fa-windows"></span>&#32;
                      {{/if}}
                      {{$charset.name|escape}}
                    </a>
                  </td>
                  {{if $charset.name === 'UTF-8'}}
                    <td>
                      <a href="{{url route="download-stats/weapon-rule-map" lang=$lang.lang charset=$charset.php_name bom=1}}">
                        {{$charset.name|escape}}(BOM)
                      </a>
                    </td>
                  {{/if}}
                {{/foreach}}
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      </li>
    </ul>
  </div>
  {{registerCss}}
    .dl-langs {
      margin-left: 2em;
    }
    .dl-langs th {
      font-weight: normal;
    }
    .dl-langs td {
      padding-left: 2ex;
    }
  {{/registerCss}}
{{/strip}}
