{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{$user->name|escape}}さんのバトル"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <h1>{{$user->name|escape}}さんのバトル</h1>

    <p>TODO</p>

    <pre><code>{{$json|escape}}</code></pre>
  </div>
{{/strip}}
