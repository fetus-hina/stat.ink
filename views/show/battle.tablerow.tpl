{{strip}}
  <tr class="battle-row" data-period="{{$model->periodId|escape}}">
    <td>
      <a href="{{url route='show/battle' screen_name=$model->user->screen_name battle=$model->id}}" class="btn btn-primary btn-xs">
        {{'Detail'|translate:'app'|escape}}
      </a>
    </td>
    <td>
      {{$model->rule->name|default:'?'|translate:'app-rule'|escape}}
    </td>
    <td>
      {{$model->map->name|default:'?'|translate:'app-map'|escape}}
    </td>
    <td>
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
    <td>
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
    </td>
    <td class="nobr">
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
    {{if $model->kill !== null && $model->death !== null}}
      {{if $model->death == 0}}
        {{if $model->kill == 0}}
          {{$ratio = 1.00}}
        {{else}}
          {{$ratio = 99.99}}
        {{/if}}
      {{else}}
        {{$ratio = $model->kill / $model->death}}
      {{/if}}

      {{* 画面上99までしか出ないのでこの処理は本来不要なはず *}}
      {{if $ratio > 99.99}}
        {{$ratio = 99.99}}
      {{elseif $ratio < -99.99}}
        {{$ratio = -99.99}}
      {{/if}}
      <td class="right visible-lg kill-ratio" data-kill-ratio="{{$ratio|escape}}">
        {{$ratio|string_format:'%.2f'|escape}}
      </td>
    {{else}}
      <td class="visible-lg"></td>
    {{/if}}
    <td>
      {{if $model->end_at === null}}
        N/A
      {{else}}
        {{$model->end_at|date_format:'%Y-%m-%d %H:%M'|escape}}
      {{/if}}
    </td>
  </tr>
{{/strip}}
