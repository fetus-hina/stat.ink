{{strip}}
  {{use class='app\assets\AppOptAsset'}}
  {{AppOptAsset::register($this)->registerCssFile($this, 'battles-simple.css')|@void}}

  <li class="simple-battle-row" data-period="{{$model->period|escape}}">
    <a href="{{url route='show/battle' screen_name=$model->user->screen_name battle=$model->id}}">
      <div class="simple-battle-row-impl">
        <div class="simple-battle-row-impl-main">
          {{if $model->is_win === null}}
            <div class="simple-battle-result simple-battle-result-unk">
              ?
            </div>
          {{else}}
            <div class="simple-battle-result simple-battle-result-{{if $model->is_win}}won{{else}}lost{{/if}}">
              {{if $model->is_win}}
                {{'Won'|translate:'app'|escape}}
              {{else}}
                {{'Lost'|translate:'app'|escape}}
              {{/if}}
              {{if $model->isGachi && $model->is_knock_out !== null}}
                <br>
                {{if $model->is_knock_out}}
                  {{'K.O.'|translate:'app'|escape}}
                {{else}}
                  {{'Time'|translate:'app'|escape}}
                {{/if}}
              {{/if}}
            </div>
          {{/if}}
          <div class="simple-battle-data">
            <div class="simple-battle-map omit">
              {{$model->map->name|default:'?'|translate:'app-map'|escape}}
            </div>
            <div class="simple-battle-rule omit">
              {{$model->rule->name|default:'?'|translate:'app-rule'|escape}}
            </div>
            <div class="simple-battle-weapon omit">
              {{$model->weapon->name|default:'?'|translate:'app-weapon'|escape}}
            </div>
            <div class="simple-battle-kill-death omit">
              {{if $model->kill !== null}}
                {{$model->kill|escape}}
              {{else}}
                ?
              {{/if}}K / {{if $model->death !== null}}
                {{$model->death|escape}}
              {{else}}
                ?
              {{/if}}D
              {{if $model->kill !== null && $model->death !== null}}
                &#32;
                {{if $model->kill == $model->death}}
                  <span class="label label-default">=</span>
                {{elseif $model->kill > $model->death}}
                  <span class="label label-success">&gt;</span>
                {{else}}
                  <span class="label label-danger">&lt;</span>
                {{/if}}
              {{/if}}
            </div>
          </div>
        </div>
        <div class="simple-battle-at">
          {{if $model->end_at}}
            {{$model->end_at|as_datetime:'short':'medium'|escape}}
          {{/if}}
        </div>
      </div>
    </a>
  </li>
{{/strip}}
