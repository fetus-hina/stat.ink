{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{$user->name|escape}}さんのイカログ"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <h1>{{$user->name|escape}}さんのイカログ</h1>
    <h2>最近の成績</h2>
    TODO

    <h2>バトル</h2>
    <div class="row">
      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
        {{ActiveForm assign="_" id="filter-form" action=['show/user', 'screen_name' => $user->screen_name] method="get"}}
          {{$_->field($filter, 'rule')->dropDownList($rules)->label(false)}}
          {{$_->field($filter, 'map')->dropDownList($maps)->label(false)}}
          {{$_->field($filter, 'weapon')->dropDownList($weapons)->label(false)}}

          TODO:勝敗<br>
          TODO:k/d<br>
          TODO:期間<br>
          <input type="submit" value="検索" class="btn btn-primary">
        {{/ActiveForm}}
      </div>
      <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9">
        TODO:バトル一覧
      </div>
    </div>
  </div>
{{/strip}}
