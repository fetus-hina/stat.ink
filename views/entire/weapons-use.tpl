{{strip}}
  {{set layout="main.tpl"}}

  {{$title = 'Weapons'|translate:'app'}}
  {{$subtitle = 'Compare Number Of Uses'|translate:'app'}}
  {{set title="{{$app->name}} | {{$subtitle}} - {{$title}}"}}

  {{use class="yii\helpers\Url"}}
  {{$_canonicalUrl = Url::to(['entire/weapons-use', 'cmp' => $form->toQueryParams('')], true)}}
  {{$this->registerLinkTag(['rel' => 'canonical', 'href' => $_canonicalUrl])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:url', 'content' => $_canonicalUrl])|@void}}

  {{use class="yii\bootstrap\Html"}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    {{registerCss}}.graph{height:300px}{{/registerCss}}

    <h2>
      {{'Compare Number Of Uses'|translate:'app'|escape}}
    </h2>
    <div id="graph-trends-legends">
    </div>
    {{$_iconAsset = $app->assetManager->getBundle('app\assets\GraphIconAsset')}}
    {{Html::tag('div', '', [
          'id' => 'graph-trends',
          'class' => 'graph',
          'data' => [
            'refs' => 'trends-data',
            'legends' => 'graph-trends-legends',
            'icon' => $app->assetManager->getAssetUrl($_iconAsset, 'dummy.png')
          ]
        ])}}
    <p class="text-right">
      <label>
        <input type="checkbox" id="stack-trends" value="1">&#32;{{'Stack'|translate:'app'|escape}}
      </label>
    </p>
    {{use class="yii\bootstrap\ActiveForm"}}
    {{$_form = ActiveForm::begin(['method' => 'GET', 'id' => 'compare-form'])}}
      <div class="form-group">
        {{Html::submitButton(
            'Update'|translate:'app',
            ['class' => 'btn btn-primary']
          )}}
      </div>
      <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">
          {{for $_i = 1; $_i <= \app\models\WeaponCompareForm::NUMBER; $_i++}}
            <div class="row">
              <div class="col-xs-6">
                {{$_form->field($form, "weapon{{$_i}}")
                  ->label(false)
                  ->dropDownList($weapons)}}
              </div>
              <div class="col-xs-6">
                {{$_form->field($form, "rule{{$_i}}")
                  ->label(false)
                  ->dropDownList($rules)}}
              </div>
            </div>
          {{/for}}
        </div>
      </div>
      <div class="form-group">
        {{Html::submitButton(
            'Update'|translate:'app',
            ['class' => 'btn btn-primary']
          )}}
      </div>
    {{ActiveForm::end()|@void}}

    <script id="trends-data" type="application/json">
      {{$data|@json_encode}}
    </script>

    {{$_depends = [
        'jp3cki\yii2\flot\FlotAsset',
        'jp3cki\yii2\flot\FlotTimeAsset',
        'jp3cki\yii2\flot\FlotStackAsset',
        'app\assets\FlotIconAsset'
      ]}}
    {{$_appAsset = $app->assetManager->getBundle('app\assets\AppAsset')}}
    {{$_url = $app->assetManager->getAssetUrl($_appAsset, 'weapons-use.js')}}
    {{$this->registerJsFile($_url, ['depends' => $_depends])|@void}}
  </div>
{{/strip}}
