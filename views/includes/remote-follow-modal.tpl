{{strip}}
  {{use class="yii\helpers\Html"}}
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
          <form action="{{url route="/show/user-remote-follow" screen_name=$user->screen_name}}" method="post" style="margin-top:15px">
            {{$_req = $app->request}}
            <input type="hidden" name="{{$_req->csrfParam|escape}}" value="{{$_req->csrfToken|escape}}">
            <div class="form-group">
              <input type="index" name="account" pattern="[a-zA-Z0-9_]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*" class="form-control" placeholder="例: example@mstdn.jp">
            </div>
            <div class="form-group">
              <input type="submit" value="指定アカウントでこのユーザをフォローする" class="btn btn-primary btn-block">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
{{/strip}}
