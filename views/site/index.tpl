{{strip}}
  {{set layout="main.tpl"}}
  <div class="container">
    <h1>
      {{$app->name|escape}}
    </h1>
    <p>
      さくせいかいし
    </p>
    <ul>
      {{foreach \app\models\User::find()->all() as $user}}
        <li>
          <a href="{{url route='show/user' screen_name=$user->screen_name}}">
            {{$user->name|escape}}
          </a>
        </li>
      {{/foreach}}
    </ul>
  </div>
{{/strip}}
