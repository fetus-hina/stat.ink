{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{$user->name|escape}}さんのバトル"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <h1>{{$user->name|escape}}さんのバトル</h1>

    <p>近いうちにまともな表示になりますたぶん</p>

    {{$image = null}}
    {{if $battle->battleImageJudge}}
      {{$image = $battle->battleImageJudge}}
    {{elseif $battle->battleImageResult}}
      {{$image = $battle->battleImageResult}}
    {{/if}}
    {{if $image}}
      <img src="{{$image->url|escape}}" style="max-width:100%;height:auto">
    {{/if}}

    <table class="table table-striped">
      <tbody>
        <tr>
          <th>{{'Rule'|translate:'app'|escape}}</th>
          <td>{{$battle->rule->name|default:'?'|translate:'app-rule'|escape}}</td>
        </tr>
        <tr>
          <th>{{'Map'|translate:'app'|escape}}</th>
          <td>{{$battle->map->name|default:'?'|translate:'app-map'|escape}}</td>
        </tr>
        <tr>
          <th>{{'Weapon'|translate:'app'|escape}}</th>
          <td>{{$battle->weapon->name|default:'?'|translate:'app-weapon'|escape}}</td>
        </tr>
        <tr>
          <th>{{'Rank'|translate:'app'|escape}}</th>
          <td>{{$battle->rank->name|default:'?'|translate:'app-rank'|escape}}</td>
        </tr>
        <tr>
          <th>{{'Level'|translate:'app'|escape}}</th>
          <td>{{$battle->level|default:'?'|escape}}</th>
        </tr>
        <tr>
          <th>{{'Result'|translate:'app'|escape}}</th>
          <td>
            {{if $battle->is_win === true}}
              {{'WON'|translate:'app'|escape}}
            {{elseif $battle->is_win === false}}
              {{'LOST'|translate:'app'|escape}}
            {{else}}
              {{'?'|translate:'app'|escape}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{'Rank in Team'|translate:'app'|escape}}</th>
          <td>{{$battle->rank_in_team|default:'?'|escape}}</td>
        </tr>
        <tr>
          <th>{{'Killed/Dead'|translate:'app'|escape}}</th>
          <td>{{$battle->kill|default:'?'|escape}} / {{$battle->death|default:'?'|escape}}</td>
        </tr>
        {{if $battle->isNawabari}}
          {{$nawabari = $battle->battleNawabari}}
          {{if $nawabari}}
            <tr>
              <th>{{'Turf Inked'|translate:'app'|escape}}</th>
              <td>{{$nawabari->my_point|default:'?'|escape}}</td>
            </tr>
          {{/if}}
        {{/if}}
        {{if $battle->isGachi}}
          {{$gachi = $battle->battleGachi}}
        {{/if}}
        <tr>
          <th>{{'Battle Start'|translate:'app'|escape}}</th>
          <td>{{$battle->start_at|date_format:'%F %T %Z'|escape}}</td>
        </tr>
        <tr>
          <th>{{'Battle End'|translate:'app'|escape}}</th>
          <td>{{$battle->end_at|date_format:'%F %T %Z'|escape}}</td>
        </tr>
      </tbody>
    </table>
  </div>
{{/strip}}
