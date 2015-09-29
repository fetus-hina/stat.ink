{{strip}}
  <tr>
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
        {{'WON'|translate:'app'|escape}}
      {{else}}
        {{'LOST'|translate:'app'|escape}}
      {{/if}}
    </td>
    <td>
      {{if $model->kill === null}}
        ?
      {{else}}
        {{$model->kill|escape}}
      {{/if}} 
    </td>
    <td>
      {{if $model->death === null}}
        ?
      {{else}}
        {{$model->death|escape}}
      {{/if}} 
    </td>
    <td>
      {{if $model->end_at === null}}
        N/A
      {{else}}
        {{$model->end_at|date_format:'%Y-%m-%d %H:%M'|escape}}
      {{/if}}
    </td>
  </tr>
{{/strip}}
