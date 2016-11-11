{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Update Your Icon'|translate:'app'}}"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <h1>
      {{'Update Your Icon'|translate:'app'|escape}}
    </h1>
    <p>
      {{'Your current icon:'|translate:'app'|escape}}&#32;
      {{if $user->userIcon}}
        <span class="profile-icon">
          <img src="{{$user->userIcon->url|escape}}" width="48" height="48">
        </span>
      {{else}}
        <span class="profile-icon">
          {{JdenticonWidget hash=$user->identiconHash class="identicon" size="48"}}
        </span>
      {{/if}}
    </p>
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
        <div class="form-group">
          {{Html::a(
              '<span class="fa fa-angle-double-left fa-fw"></span>'|cat:Yii::t('app', 'Back'),
              ['user/profile'],
              ['class' => 'btn btn-default']
            )}}
        </div>
        {{if $app->params['twitter']['read_enabled']}}
          <div class="panel panel-default">
            <div class="panel-heading">
              {{'Use profile icon of your twitter account'|translate:'app'|escape}}
            </div>
            <div class="panel-body">
              <p class="text-right">
                <a href="{{url route="icon-twitter"}}" class="btn btn-info btn-block">
                  <span class="fa fa-twitter left" style="color:#fff!important"></span>
                  {{'Use your profile icon'|translate:'app'|escape}}
                </a>
              </p>
            </div>
          </div>
        {{/if}}
        <div class="panel panel-default">
          <div class="panel-heading">
            {{'Upload new image'|translate:'app'|escape}}
          </div>
          <div class="panel-body">
            {{Html::beginForm('edit-icon', 'post', ['enctype' => 'multipart/form-data'])}}
              <input type="hidden" name="action" value="update">
              <ul>
                <li>
                  {{'PNG/JPEG file up to {0}'|translate:'app':'1 MiB'|escape}}
                </li>
                <li>
                  {{'{0}×{1} or less resolution'|translate:'app':[1000,1000]|escape}}
                </li>
              </ul>
              <div class="form-group">
                <input type="file" name="image" value="" class="" required>
              </div>
              <button type="submit" class="btn btn-info btn-block"> 
                <span class="fa fa-upload left"></span>
                {{'Upload icon'|translate:'app'|escape}}
              </button>
            {{Html::endForm()}}
          </div>
        </div>
        {{if $current}}
          <div class="panel panel-default">
            <div class="panel-heading">
              {{'Reset to default icon'|translate:'app'|escape}}
            </div>
            <div class="panel-body">
              {{Html::beginForm('edit-icon', 'post')}}
                <input type="hidden" name="action" value="delete">
                <p>
                  {{'Your current image will be deleted and reset to auto-generated image.'|translate:'app'|escape}}
                </p>
                <p>
                  {{'The icon will be:'|translate:'app'|escape}}&#32;
                  {{JdenticonWidget hash=$user->identiconHash class="identicon" size="48"}}
                </p>
                <p class="text-right">
                  <button class="btn btn-danger btn-block"> 
                    <span class="fa fa-undo left"></span>
                    {{'Reset icon'|translate:'app'|escape}}
                  </button>
                </p>
              {{Html::endForm()}}
            </div>
          </div>
        {{/if}}
        <div class="form-group">
          {{Html::a(
              '<span class="fa fa-angle-double-left fa-fw"></span>'|cat:Yii::t('app', 'Back'),
              ['user/profile'],
              ['class' => 'btn btn-default']
            )}}
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
