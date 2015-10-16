{{strip}}
  <tr class="battle-row" data-period="{{$model->periodId|escape}}">
    <td>
      <a href="{{url route='show/battle' screen_name=$model->user->screen_name battle=$model->id}}" class="btn btn-primary btn-xs">
        {{'Detail'|translate:'app'|escape}}
      </a>
    </td>
    <td class="cell-lobby">
      {{$model->lobby->name|default:'?'|translate:'app-rule'|escape}}
    </td>
    <td class="cell-rule">
      {{$model->rule->name|default:'?'|translate:'app-rule'|escape}}
    </td>
    <td class="cell-map">
      {{$model->map->name|default:'?'|translate:'app-map'|escape}}
    </td>
    <td class="cell-main-weapon">
      {{if $model->weapon}}
        <span title="{{*
            *}}{{'Sub:'|translate:'app'|escape}}{{$model->weapon->subweapon->name|default:'?'|translate:'app-subweapon'|escape}} / {{*
            *}}{{'Special:'|translate:'app'|escape}}{{$model->weapon->special->name|default:'?'|translate:'app-special'|escape}}" class="auto-tooltip">
          {{$model->weapon->name|default:'?'|translate:'app-weapon'|escape}}
        </span>
      {{else}}
        ?
      {{/if}}
    </td>
    <td class="cell-sub-weapon">
      {{$model->weapon->subweapon->name|default:'?'|translate:'app-subweapon'|escape}}
    </td>
    <td class="cell-special">
      {{$model->weapon->special->name|default:'?'|translate:'app-special'|escape}}
    </td>
    <td class="cell-rank">
      {{$model->rank->name|default:'?'|translate:'app-rank'|escape}}
    </td>
    <td class="cell-level">
      {{$model->level|default:'?'|escape}}
    </td>
    <td class="cell-result">
      {{if $model->is_win === null}}
        ?
      {{elseif $model->is_win}}
        <span class="label label-success">
          {{'WON'|translate:'app'|escape}}
        </span>
      {{else}}
        <span class="label label-danger">
          {{'LOST'|translate:'app'|escape}}
        </span>
      {{/if}}
      {{if $model->isGachi && $model->is_knock_out !== null}}
        &nbsp;
        {{if $model->is_knock_out}}
          <span class="label label-info auto-tooltip" title="{{'KNOCK OUT'|translate:'app'|escape}}">
            {{'K.O.'|translate:'app'|escape}}
          </span>
        {{else}}
          <span class="label label-warning auto-tooltip" title="{{'TIME IS UP'|translate:'app'|escape}}">
            {{'TIME'|translate:'app'|escape}}
          </span>
        {{/if}}
      {{/if}}
    </td>
    <td class="cell-kd nobr">
      <span class="kill">
        {{if $model->kill === null}}
          ?
        {{elseif $model->death !== null && $model->kill >= $model->death}}
          <strong>{{$model->kill|escape}}</strong>
        {{else}}
          {{$model->kill|escape}}
        {{/if}} 
      </span> / <span class="death">
        {{if $model->death === null}}
          ?
        {{elseif $model->kill !== null && $model->kill <= $model->death}}
          <strong>{{$model->death|escape}}</strong>
        {{else}}
          {{$model->death|escape}}
        {{/if}} 
      </span>
      {{if $model->kill !== null && $model->death !== null}}
        &#32;
        {{if $model->kill > $model->death}}
          <span class="label label-success">&gt;</span>
        {{elseif $model->kill < $model->death}}
          <span class="label label-danger">&lt;</span>
        {{else}}
          <span class="label label-default">=</span>
        {{/if}}
      {{/if}}
    </td>
    {{if $model->kill_ratio !== null}}
      <td class="right kill-ratio cell-kill-ratio" data-kill-ratio="{{$model->kill_ratio|escape}}">
        {{$model->kill_ratio|string_format:'%.2f'|escape}}
      </td>
    {{else}}
      <td class="cell-kill-ratio"></td>
    {{/if}}
    <td class="cell-point">
      {{$model->my_point|default:'?'|escape}}
    </td>
    </td>
    <td class="cell-datetime">
      {{if $model->end_at === null}}
        {{'N/A'|translate:'app'|escape}}
      {{else}}
        {{$model->end_at|date_format:'%Y-%m-%d %H:%M'|escape}}
      {{/if}}
    </td>
    <td class="cell-reltime">
      {{if $model->end_at === null}}
        {{'N/A'|translate:'app'|escape}}
      {{else}}
        {{$t = $model->end_at|strtotime}}
        <span class="auto-tooltip" title="{{$model->end_at|date_format:'%Y-%m-%d %H:%M %Z'|escape}}">
          {{$app->formatter->asRelativeTime($t)|escape}}
        </span>
      {{/if}}
    </td>
  </tr>
{{/strip}}
