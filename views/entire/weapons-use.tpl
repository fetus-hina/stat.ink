{{strip}}
  {{set layout="main.tpl"}}

  {{$title = 'Weapons'|translate:'app'}}
  {{$subtitle = 'Compare Number Of Uses'|translate:'app'}}
  {{set title="{{$app->name}} | {{$subtitle}} - {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}

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
    <div id="graph-trends" data-refs="trends-data" class="graph">
    </div>
    <p class="text-right">
      <label>
        <input type="checkbox" id="stack-trends" value="1">&#32;{{'Stack'|translate:'app'|escape}}
      </label>
    </p>
    {{use class="yii\bootstrap\ActiveForm"}}
    {{$_form = ActiveForm::begin(['method' => 'GET', 'id' => 'compare-form'])}}
      <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">
          {{$_class = "col-xs-6"}}
          <div class="row">
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'weapon1')
                ->label(false)
                ->dropDownList($weapons)}}
            </div>
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'rule1')
                ->label(false)
                ->dropDownList($rules)}}
            </div>
          </div>
          <div class="row">
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'weapon2')
                ->label(false)
                ->dropDownList($weapons)}}
            </div>
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'rule2')
                ->label(false)
                ->dropDownList($rules)}}
            </div>
          </div>
          <div class="row">
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'weapon3')
                ->label(false)
                ->dropDownList($weapons)}}
            </div>
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'rule3')
                ->label(false)
                ->dropDownList($rules)}}
            </div>
          </div>
          <div class="row">
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'weapon4')
                ->label(false)
                ->dropDownList($weapons)}}
            </div>
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'rule4')
                ->label(false)
                ->dropDownList($rules)}}
            </div>
          </div>
          <div class="row">
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'weapon5')
                ->label(false)
                ->dropDownList($weapons)}}
            </div>
            <div class="{{$_class|escape}}">
              {{$_form->field($form, 'rule5')
                ->label(false)
                ->dropDownList($rules)}}
            </div>
          </div>
        </div>
      </div>
      {{Html::submitButton(
          'Update'|translate:'app',
          ['class' => 'btn btn-primary']
        )}}
    {{ActiveForm::end()|@void}}

    <script id="trends-data" type="application/json">
      {{$data|@json_encode}}
    </script>

    {{$_depends = [
        'jp3cki\yii2\flot\FlotAsset',
        'jp3cki\yii2\flot\FlotTimeAsset',
        'jp3cki\yii2\flot\FlotStackAsset'
      ]}}
    {{$_appAsset = $app->assetManager->getBundle('app\assets\AppAsset')}}
    {{$_url = $app->assetManager->getAssetUrl($_appAsset, 'weapons-use.js')}}
    {{$this->registerJsFile($_url, ['depends' => $_depends])|@void}}
  </div>
{{/strip}}
