{{strip}}
  {{\hiqdev\assets\flagiconcss\FlagIconCssAsset::register($this)|@void}}
  {{\app\assets\DownloadsPageAsset::register($this)|@void}}
  {{use class="app\models\Language"}}
  {{$langs = Language::find()
      ->with('languageCharsets')
      ->orderBy('{{language}}.[[name]] ASC')
      ->asArray()
      ->all()}}

  <ul class="dl-langs">
    {{foreach $langs as $lang}}
      <li>
        <span class="lang">
          <span class="flag-icon flag-icon-{{$lang.lang|substr:3:2|strtolower|escape}}"></span>&#32;{{$lang.name|escape}}
        </span>
        <span class="charsets">
          {{foreach $lang.languageCharsets as $_charset}}
            {{$charset = $_charset.charset}}
            <span class="charset">
              <a href="{{url route=$route lang=$lang.lang charset=$charset.php_name}}" hreflang="{{$lang.lang|escape}}" rel="nofollow">
                {{if $_charset.is_win_acp}}
                  <span class="fa fa-windows"></span>&#32;
                {{/if}}
                {{$charset.name|escape}}
              </a>
            </span>
            {{if $charset.name === 'UTF-8'}}
              <span class="charset">
                <a href="{{url route=$route lang=$lang.lang charset=$charset.php_name bom=1}}" hreflang="{{$lang.lang|escape}}" rel="nofollow">
                  {{$charset.name|escape}}(BOM)
                </a>
              </span>
            {{elseif $charset.name === 'UTF-16LE'}}
              <span class="charset">
                <a href="{{url route=$route lang=$lang.lang charset=$charset.php_name tsv=1}}" hreflang="{{$lang.lang|escape}}" rel="nofollow">
                  <span class="fa fa-apple"></span>&#32;{{$charset.name|escape}}(TSV)
                </a>
              </span>
            {{/if}}
          {{/foreach}}
        </span>
      </li>
    {{/foreach}}
  </ul>
{{/strip}}
