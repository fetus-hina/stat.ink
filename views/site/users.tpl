{{strip}}
  {{set layout="main.tpl"}}
  {{use class="app\models\Battle"}}
  <div class="container">
    <h1>
      {{'All Players'|translate:'app':$app->name|escape}}
    </h1>

    <div style="margin-bottom:15px">
      {{include file="@app/views/includes/ad.tpl"}}
    </div>

    {{SnsWidget}}

    {{include file="@app/views/includes/battle_thumb_list.tpl" battles=$battles}}
{{/strip}}
