{{strip}}
  {{set layout="main.tpl"}}

  {{$title = 'Weapons'|translate:'app'}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    <h2>
      Weapons
    </h2>
    <p>
      {{'Excluded: Posted Player, All Players(when Private Battle), Posted Player\'s Team Member(when Squad Battle)'|translate:'app'|escape}}
    </p>
    {{foreach $entire as $rule}}
      {{if $rule->data->battle_count > 0}}
        <h3>
          {{$rule->name|escape}} 
        </h3>
        <p>
          n={{$rule->data->battle_count|number_format|escape}}
        </p>
        <table class="table table-striped table-condensed">
          <thead>
            <tr>
              <th>{{'Weapon'|translate:'app'|escape}}</th>
              <th>{{'Count'|translate:'app'|escape}}</th>
              <th>{{'Avg Killed'|translate:'app'|escape}}</th>
              <th>{{'Avg Dead'|translate:'app'|escape}}</th>
              <th>{{'Avg KR'|translate:'app'|escape}}</th>
              <th>{{'WP'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{foreach $rule->data->weapons as $weapon}}
              <tr>
                <th>{{$weapon->name|escape}}</th>
                <td>{{$weapon->count|number_format|escape}}</td>
                <td>{{$weapon->avg_kill|string_format:'%.1f'|escape}}</td>
                <td>{{$weapon->avg_death|string_format:'%.1f'|escape}}</td>
                <td>
                  {{if $weapon->avg_death == 0}}
                    {{if $weapon->avg_kill > 0}}
                      99.99
                    {{/if}}
                  {{else}}
                    {{($weapon->avg_kill/$weapon->avg_death)|string_format:'%.2f'|escape}}
                  {{/if}}
                </td>
                <td>{{$weapon->wp|string_format:'%.2f%%'|escape}}</td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      {{/if}}
    {{/foreach}}

    <h2>
      {{'Favorite Weapons'|translate:'app'|escape}}
    </h2>
    <table class="table table-striped table-condensed">
      <tbody>
        {{$_max = 0}}
        {{foreach $users as $_w}}
          <tr>
            <td class="text-right" style="width:15em">
              {{$_w->weapon->name|default:'?'|translate:'app-weapon'|escape}}
            </td>
            <td>
              {{if $_max < $_w->user_count}}
                {{$_max = $_w->user_count}}
              {{/if}}
              {{if $_max > 0}}
                <div class="progress">
                  <div class="progress-bar" role="progressbar" style="width:{{($_w->user_count*100/$_max)|escape}}%;">
                    {{$_w->user_count|number_format|escape}}
                  </div>
                </div>
              {{/if}}
            </td>
          </tr>
        {{/foreach}}
      </tbody>
    </table>
  </div>
{{/strip}}
