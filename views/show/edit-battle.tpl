{{strip}}
  {{set layout="main.tpl"}}
  {{$title = "Edit Your Battle: #{0}"|translate:'app':$battle->id}}
  {{set title="{{$app->name}} | {{$title}}"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{if $battle->battleImageJudge || $battle->battleImageResult}}
      <div class="row">
        {{if $battle->battleImageJudge}}
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 image-container">
            <img src="{{$battle->battleImageJudge->url|escape}}" style="max-width:100%;height:auto">
          </div>
        {{/if}}
        {{if $battle->battleImageResult}}
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 image-container">
            <img src="{{$battle->battleImageResult->url|escape}}" style="max-width:100%;height:auto">
          </div>
          {{$this->registerMetaTag(['name' => 'twitter:image', 'content' => $battle->battleImageResult->url])|@void}}
        {{/if}}
      </div>
    {{/if}}

    {{ActiveForm assign="_" id="edit-form" action=['show/edit-battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id]}}
      <table class="table table-striped">
        <tbody>
          <tr>
            <th>
              {{'Game Mode'|translate:'app'|escape}}
            </th>
            <td>
              {{$_->field($form, 'lobby_id')->label(false)->dropDownList($lobbies)}}
            </td>
          </tr>
          <tr>
            <th>
              {{'Rule'|translate:'app'|escape}}
            </th>
            <td>
              {{$_->field($form, 'rule_id')->label(false)->dropDownList($rules)}}
            </td>
          </tr>
          <tr>
            <th>
              {{'Map'|translate:'app'|escape}}
            </th>
            <td>
              {{$_->field($form, 'map_id')->label(false)->dropDownList($maps)}}
            </td>
          </tr>
          <tr>
            <th>
              {{'Weapon'|translate:'app'|escape}}
            </th>
            <td>
              {{$_->field($form, 'weapon_id')->label(false)->dropDownList($weapons)}}
            </td>
          </tr>
        </tbody>
      </table>
      {{Html::submitButton(
          Yii::t('app', 'Update'),
          ['class' => 'btn btn-lg btn-primary btn-block']
        )}}
    {{/ActiveForm}}
    <div style="margin-top:15px">
      {{Html::a(
          Yii::t('app', 'Back'),
          ['show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
          ['class' => 'btn btn-lg btn-default btn-block']
        )}}
    </div>
  </div>
{{/strip}}
{{registerCss}}{{literal}}
th{width:15em}
@media(max-width:30em){th{width:auto}}
.image-container{margin-bottom:15px}
{{/literal}}{{/registerCss}}
