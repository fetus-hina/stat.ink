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
              {{'Lobby'|translate:'app'|escape}}
            </th>
            <td>
              {{$_->field($form, 'lobby_id')->label(false)->dropDownList($lobbies)}}
            </td>
          </tr>
          <tr>
            <th>
              {{'Mode'|translate:'app'|escape}}
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

    <hr>

    <div style="margin-top:7.5em;border:1px solid #ccc;border-radius:5px;padding:15px">
      <h2 style="color:#c9302c">
        {{'Danger Zone'|translate:'app'|escape}}
      </h2>
      <p>
        {{'You can delete this battle.'|translate:'app'|escape}}
      </p>
      <ul>
        <li>
          {{'If you delete this battle, it will be gone forever.'|translate:'app'|escape}}
        </li>
        <li>
          <strong style="color:#c9302c">
            {{'Please do not use this feature to destroy evidence.'|translate:'app'|escape}}
          </strong>
          &#32;
          {{'This action is provided for deleting a falsely-recognized battle.'|translate:'app'|escape}}
        </li>
        <li>
          {{'If you misuse this feature, you will be banned.'|translate:'app'|escape}}
        </li>
      </ul>
      {{ActiveForm assign="_" id="delete-form" action=['show/edit-battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id]}}
        {{Html::hiddenInput('_action', 'delete')}}
        {{$_->field($delete, 'agree')
            ->label(Yii::t('app', 'I agree. Delete this battle.'))
            ->checkbox(['value' => 'yes', 'uncheck' => null])}}
        {{Html::submitButton(
            Yii::t('app', 'Delete'),
            ['class' => 'btn btn-lg btn-danger btn-block']
          )}}
      {{/ActiveForm}}
    </div>
  </div>
{{/strip}}
{{registerCss}}{{literal}}
th{width:15em}
@media(max-width:30em){th{width:auto}}
.image-container{margin-bottom:15px}
{{/literal}}{{/registerCss}}
