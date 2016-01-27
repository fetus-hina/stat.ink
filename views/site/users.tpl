{{strip}}
  {{set layout="main.tpl"}}
  {{use class="app\models\Battle"}}
  <div class="container">
    <h1>
      {{'All Players'|translate:'app':$app->name|escape}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    {{capture name=pager assign=pager}}
      {{use class="yii\widgets\ListView"}}
      <div class="text-right">
        {{ListView::widget([
            'dataProvider' => $battles,
            'itemView' => null,
            'itemOptions' => [ 'tag' => false ],
            'layout' => '{pager}',
            'pager' => [
              'maxButtonCount' => 5
            ]
          ])}}
      </div>
    {{/capture}}

    {{$pager}}
    {{include file="@app/views/includes/battle_thumb_list.tpl" battles=$battles->getModels()}}
    {{$pager}}
{{/strip}}
