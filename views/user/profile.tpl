{{strip}}
  {{\app\assets\ZxcvbnAsset::register($this)|@void}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | ユーザ情報"}}
  <div class="container">
    <h1>ユーザ情報</h1>
    <p>まだ編集機能は作っていません</p>
    <p>APIキーは他人に知られないようご注意ください。</p>
    <table class="table table-striped">
      <tbody>
        <tr>
          <th>内部ID</th>
          <td>#{{$user->id|escape}}</td>
        </tr>
        <tr>
          <th>ログイン名</th>
          <td><code>{{$user->screen_name|escape}}</code></td>
        </tr>
        <tr>
          <th>パスワード</th>
          <td>********</td>
        </tr>
        <tr>
          <th>APIキー</th>
          <td><code>{{$user->api_key|escape}}</code></td>
        </tr>
      </tbody>
    </table>
  </div>
{{/strip}}
