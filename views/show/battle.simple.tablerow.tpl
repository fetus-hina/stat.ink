{{strip}}
  <li class="simple-battle-row" data-period="{{$model->period|escape}}">
    <a href="{{url route='show/battle' screen_name=$model->user->screen_name battle=$model->id}}">
      <div class="simple-battle-row-impl">
        {{if $model->is_win === null}}
          <div class="simple-battle-result simple-battle-result-unk">
            ?
          </div>
        {{else}}
          <div class="simple-battle-result simple-battle-result-{{if $model->is_win}}won{{else}}lost{{/if}}">
            {{if $model->is_win}}
              {{'WON'|translate:'app'|escape}}
            {{else}}
              {{'LOST'|translate:'app'|escape}}
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
          <div class="simple-battle-rule-map omit">
            {{$model->map->name|default:'?'|translate:'app-map'|escape}}
          </div>
          <div class="simple-battle-weapon omit">
            {{$model->rule->name|default:'?'|translate:'app-rule'|escape}}
            &#32;/&#32;
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
        <div class="simple-battle-at">
          {{if $model->end_at}}
            {{$model->end_at|date_format:'%Y-%m-%d %H:%M'|escape}}
          {{/if}}
        </div>
      </div>
    </a>
  </li>
  {{registerCss}}
    .simple-battle-row{
      display:block;
      list-style-type:none;
      margin:1em 0;
      padding:0.618em;
      box-shadow:2px 2px 3px rgba(0,0,0,.1);
      border-radius:4px;
      border:1px solid #ccc;
      background-color:#f9f9f9;
    }

    .simple-battle-row a,
    .simple-battle-row a:link,
    .simple-battle-row a:visited,
    .simple-battle-row a:hover,
    .simple-battle-row a:active{
      color:#333;
      text-decoration:none;
    }

    .simple-battle-row-impl{
      display:table;
      position:relative;
      width:100%;
    }

    .simple-battle-result,
    .simple-battle-data{
      display:table-cell;
      vertical-align:middle;
      max-width:1px;
    }

    .simple-battle-result{
      font-size:1.2em;
      font-weight:bold;
      text-align:center;
      width:5em;
      max-width:initial;
    }
    
    .simple-battle-result-unk{
      color:#888;
    }

    .simple-battle-result-won{
      color:#3169b3;
    }

    .simple-battle-result-lost{
      color:#ec6110;
    }

    .simple-battle-rule-map{
      font-size:1.1em;
      font-weight:bold;
    }

    .simple-battle-rule {
      font-size:{{(1/1.1)}}em;
      font-weight:normal;
    }

    .simple-battle-weapon{
      font-size:0.8em;
    }

    .simple-battle-at{
      position:absolute;
      bottom:0;
      right:0;
      color:#888;
      font-size:0.8em;
    }
  {{/registerCss}}
{{/strip}}
