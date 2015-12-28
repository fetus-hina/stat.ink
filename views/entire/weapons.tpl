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

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        {{include file="@app/views/includes/ad.tpl"}}
      </div>
    </div>

    <div id="sns">
      {{TweetButton}}
    </div>

    <h2>
      {{'Weapons'|translate:'app'|escape}}
    </h2>
    <p>
      {{'Excluded: Posted Player, All Players(when Private Battle), Posted Player\'s Team Member(when Squad Battle or Splatfest Battle)'|translate:'app'|escape}}
    </p>
    <p>
      ※できる限り重複カウントしないように除外設定を行っていますが、連戦やナワバリフレンド合流の影響により重複カウントしやすい状況が発生します。
    </p>
    {{\app\assets\JqueryStupidTableAsset::register($this)|@void}}
    {{foreach $entire as $rule}}
      {{if !$rule@first}} | {{/if}}
      <a href="#weapon-{{$rule->key|escape}}">{{$rule->name|escape}}</a>
    {{/foreach}}
    {{foreach $entire as $rule}}
      {{if $rule->data->battle_count > 0}}
        <h3 id="weapon-{{$rule->key|escape}}">
          {{$rule->name|escape}} 
        </h3>
        <p>
          {{'Battles:'|translate:'app'|escape}} {{$rule->data->battle_count|number_format|escape}},&#32;
          {{'Players:'|translate:'app'|escape}} {{$rule->data->player_count|number_format|escape}}
        </p>
        <table class="table table-striped table-condensed table-sortable">
          <thead>
            <tr>
              <th data-sort="string">{{'Weapon'|translate:'app'|escape}}</th>
              <th data-sort="int">{{'Players'|translate:'app'|escape}} <span class="arrow fa fa-angle-down"></span></th>
              <th data-sort="float">{{'Avg Killed'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg Dead'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
              {{if $rule->key === 'nawabari'}}
                <th data-sort="float">{{'Avg Inked'|translate:'app'|escape}}</th>
              {{/if}}
              <th data-sort="float">{{'Win%'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{foreach $rule->data->weapons as $weapon}}
              <tr class="weapon">
                <td>
                  <span title="{{'Sub:'|translate:'app'|escape}}{{$weapon->subweapon->name|escape}} / {{'Special:'|translate:'app'|escape}}{{$weapon->special->name|escape}}" class="auto-tooltip">
                    {{$weapon->name|escape}}
                  </span>
                </td>
                <td class="players" title="{{if $weapon->count > 0}}{{($rule->data->player_count*100/$weapon->count)|string_format:'%.2f%%'|escape}}{{/if}}" data-sort-value="{{$weapon->count|escape}}">
                  {{if $rule->data->player_count > 0}}
                    <span class="auto-tooltip" title="{{($weapon->count*100/$rule->data->player_count)|string_format:'%.2f%%'|escape}}">
                      {{$weapon->count|number_format|escape}}
                    </span>
                  {{else}}
                    0
                  {{/if}}
                </td>
                <td class="kill" data-sort-value="{{$weapon->avg_kill|escape}}">{{$weapon->avg_kill|string_format:'%.2f'|escape}}</td>
                <td class="death" data-sort-value="{{$weapon->avg_death|escape}}">{{$weapon->avg_death|string_format:'%.2f'|escape}}</td>
                {{if $weapon->avg_death == 0}}
                  {{if $weapon->avg_kill > 0}}
                    {{$kr = 99.99}}
                  {{else}}
                    {{$kr = null}}
                  {{/if}}
                {{else}}
                  {{$kr = $weapon->avg_kill / $weapon->avg_death}}
                {{/if}}
                <td data-sort-value="{{$kr|escape}}">
                  {{if $kr !== null}}
                    {{$kr|string_format:'%.2f'|escape}}
                  {{/if}}
                </td>
                {{if $rule->key === 'nawabari'}}
                  <td data-sort-value="{{if $weapon->avg_inked === null}}-1{{else}}{{$weapon->avg_inked|escape}}{{/if}}">
                    {{$weapon->avg_inked|string_format:'%.1f'|escape}}
                  </td>
                {{/if}}
                <td data-sort-value="{{$weapon->wp|escape}}">
                  {{$weapon->wp|string_format:'%.2f%%'|escape}}
                </td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>

        <table class="table table-striped table-condensed table-sortable" id="sub-{{$rule->key|escape}}">
          <thead>
            <tr>
              <th data-sort="string">{{'Sub Weapon'|translate:'app'|escape}}</th>
              <th data-sort="int">{{'Players'|translate:'app'|escape}} <span class="arrow fa fa-angle-down"></span></th>
              <th data-sort="float">{{'Avg Killed'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg Dead'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Win%'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Encounter Ratio'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{foreach $rule->sub as $weapon}}
              <tr class="weapon">
                <td>
                  {{$weapon->name|escape}}
                </td>
                <td class="players" title="{{if $weapon->count > 0}}{{($rule->data->player_count*100/$weapon->count)|string_format:'%.2f%%'|escape}}{{/if}}" data-sort-value="{{$weapon->count|escape}}">
                  {{if $rule->data->player_count > 0}}
                    <span class="auto-tooltip" title="{{($weapon->count*100/$rule->data->player_count)|string_format:'%.2f%%'|escape}}">
                      {{$weapon->count|number_format|escape}}
                    </span>
                  {{else}}
                    0
                  {{/if}}
                </td>
                <td class="kill" data-sort-value="{{$weapon->avg_kill|escape}}">{{$weapon->avg_kill|string_format:'%.2f'|escape}}</td>
                <td class="death" data-sort-value="{{$weapon->avg_death|escape}}">{{$weapon->avg_death|string_format:'%.2f'|escape}}</td>
                {{if $weapon->avg_death == 0}}
                  {{if $weapon->avg_kill > 0}}
                    {{$kr = 99.99}}
                  {{else}}
                    {{$kr = null}}
                  {{/if}}
                {{else}}
                  {{$kr = $weapon->avg_kill / $weapon->avg_death}}
                {{/if}}
                <td data-sort-value="{{$kr|escape}}">
                  {{if $kr !== null}}
                    {{$kr|string_format:'%.2f'|escape}}
                  {{/if}}
                </td>
                <td data-sort-value="{{$weapon->wp|escape}}">
                  {{$weapon->wp|string_format:'%.2f%%'|escape}}
                </td>
                <td data-sort-value="{{$weapon->encounter_4|escape}}">
                  {{$weapon->encounter_4|string_format:'%.2f%%'|escape}}
                </td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>

        <table class="table table-striped table-condensed table-sortable" id="special-{{$rule->key|escape}}">
          <thead>
            <tr>
              <th data-sort="string">{{'Special'|translate:'app'|escape}}</th>
              <th data-sort="int">{{'Players'|translate:'app'|escape}} <span class="arrow fa fa-angle-down"></span></th>
              <th data-sort="float">{{'Avg Killed'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg Dead'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Win%'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Encounter Ratio'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{foreach $rule->special as $weapon}}
              <tr class="weapon">
                <td>
                  {{$weapon->name|escape}}
                </td>
                <td class="players" title="{{if $weapon->count > 0}}{{($rule->data->player_count*100/$weapon->count)|string_format:'%.2f%%'|escape}}{{/if}}" data-sort-value="{{$weapon->count|escape}}">
                  {{if $rule->data->player_count > 0}}
                    <span class="auto-tooltip" title="{{($weapon->count*100/$rule->data->player_count)|string_format:'%.2f%%'|escape}}">
                      {{$weapon->count|number_format|escape}}
                    </span>
                  {{else}}
                    0
                  {{/if}}
                </td>
                <td class="kill" data-sort-value="{{$weapon->avg_kill|escape}}">{{$weapon->avg_kill|string_format:'%.2f'|escape}}</td>
                <td class="death" data-sort-value="{{$weapon->avg_death|escape}}">{{$weapon->avg_death|string_format:'%.2f'|escape}}</td>
                {{if $weapon->avg_death == 0}}
                  {{if $weapon->avg_kill > 0}}
                    {{$kr = 99.99}}
                  {{else}}
                    {{$kr = null}}
                  {{/if}}
                {{else}}
                  {{$kr = $weapon->avg_kill / $weapon->avg_death}}
                {{/if}}
                <td data-sort-value="{{$kr|escape}}">
                  {{if $kr !== null}}
                    {{$kr|string_format:'%.2f'|escape}}
                  {{/if}}
                </td>
                <td data-sort-value="{{$weapon->wp|escape}}">
                  {{$weapon->wp|string_format:'%.2f%%'|escape}}
                </td>
                <td data-sort-value="{{$weapon->encounter_4|escape}}">
                  {{$weapon->encounter_4|string_format:'%.2f%%'|escape}}
                </td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      {{/if}}
    {{/foreach}}
    {{registerJs}}
      (function(){
        $('.table-sortable')
          .stupidtable()
          .on("aftertablesort",function(event,data){
            var th = $(this).find("th");
            th.find(".arrow").remove();
            var dir = $.fn.stupidtable.dir;
            var arrow = data.direction === dir.ASC ? "fa-angle-up" : "fa-angle-down";
            th.eq(data.column)
              .append(' ')
              .append(
                $('<span/>').addClass('arrow fa').addClass(arrow)
              );
          });
      })();
    {{/registerJs}}

    <h2>
      {{'Favorite Weapons of This Site Member'|translate:'app'|escape}}
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
