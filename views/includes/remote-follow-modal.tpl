{{strip}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\helpers\Url"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  {{use class="rmrevin\yii\fontawesome\FontAwesome" as="FA"}}
  {{\rmrevin\yii\fontawesome\AssetBundle::register($this)|@void}}
  {{\app\assets\RemoteFollowAsset::register($this)|@void}}

  {{$_prefix = 'remote-follow-modal-internal'|sha1|substr:0:8}}

  <div class="modal fade" id="remoteFollowModal" tabindex="-1" role="dialog" aria-labelledby="remoteFollowModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="{{'Close'|translate:'app'|escape}}">
            <span aria-hidden="true">{{FA::icon('times')->tag('span')}}</span>
          </button>
          <h4 class="modal-title" id="remoteFollowModalLabel">
            <img src="{{$app->assetManager->getAssetUrl($_asset, 'ostatus.min.svg')|escape}}" style="width:auto;height:1em;vertical-align:baseline">&#32;
            {{'Remote Follow'|translate:'app'|escape}} (@{{$user->screen_name|escape}}@{{$app->request->hostName|escape}})
          </h4>
        </div>
        <div class="modal-body">
          <p>
            マストドンなどのOStatus対応サービスを利用して、バトル結果を購読することができます。
          </p>
          <p>
            このユーザ（@{{$user->screen_name|escape}}@{{$app->request->hostName|escape}}）をフォローする、あなたのアカウント名を「ユーザ名@サーバ」の形式で入力してください。<br>
            例えば、mstdn.jp の利用者であれば「<code>your_id@mstdn.jp</code>」、Pawoo の利用者であれば「<code>your_id@pawoo.net</code>」です。
          </p>
          <hr>
          <div style="margin-top:15px">
            {{use class="app\models\RemoteFollowModalForm"}}
            {{use class="yii\bootstrap\ActiveForm"}}
            {{$_form = RemoteFollowModalForm::factory()}}
            {{$_ = ActiveForm::begin([
              'action' => Url::to(['/ostatus/start-remote-follow', 'screen_name' => $user->screen_name])
            ])}}
              {{$_->field($_form, 'screen_name')
                  ->hiddenInput(['value' => $user->screen_name])
                  ->label(false)}}
              {{$_->field($_form, 'account')
                  ->textInput(['placeholder' => '例: your_id@mstdn.jp'])
                  ->label('あなたのアカウント')
                }}
              <div class="form-group">
                <input type="submit" value="指定アカウントでこのユーザをフォローする" class="btn btn-primary btn-block">
              </div>
            {{ActiveForm::end()|@void}}
          </div>
        </div>
      </div>
    </div>
  </div>
{{/strip}}
