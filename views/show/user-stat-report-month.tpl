{{strip}}
  {{set layout="main.tpl"}}

  {{$title = "{0}'s Battle Report"|translate:'app':$user->name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->userIcon->absUrl|default:$user->jdenticonPngUrl])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}
  {{if $next}}
    {{$this->registerLinkTag(['rel' => 'next', 'href' => $next])}}
  {{/if}}
  {{if $prev}}
    {{$this->registerLinkTag(['rel' => 'prev', 'href' => $prev])}}
  {{/if}}
  <div class="container">
    <h1>
      {{use class="yii\helpers\Url"}}
      {{$_url = Url::to(['show/user', 'screen_name' => $user->screen_name])}}
      {{$name = $user->name|escape}}
      {{$name = '<a href="%s">%s</a>'|sprintf:$_url:$name}}
      {{"{0}'s Battle Report"|translate:'app':$name}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    {{if $next || $prev}}
      <div class="row">
        {{if $prev}}
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <a href="{{$prev|escape}}" class="btn btn-default">
              <span class="fa fa-angle-double-left left"></span>{{'Prev. Month'|translate:'app'|escape}}
            </a>
          </div>
        {{/if}}
        {{if $next}}
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pull-right text-right">
            <a href="{{$next|escape}}" class="btn btn-default">
              {{'Next Month'|translate:'app'|escape}}<span class="fa fa-angle-double-right right"></span>
            </a>
          </div>
        {{/if}}
      </div>
    {{/if}}
    <div class="table-responsive">
      <table class="table table-striped table-condensed">
        <thead>
          <tr>
            <th></th>
            <th>{{'Lobby'|translate:'app'|escape}}</th>
            <th>{{'Mode'|translate:'app'|escape}}</th>
            <th>{{'Stage'|translate:'app'|escape}}</th>
            <th>{{'Weapon'|translate:'app'|escape}}</th>
            <th>{{'Battles'|translate:'app'|escape}}</th>
            <th>{{'Win %'|translate:'app'|escape}}</th>
            <th>{{'k/d'|translate:'app'|escape}}</th>
            <th>{{'KR'|translate:'app'|escape}}</th>
          </tr>
        <tbody>
          {{$_last_date = null}}
          {{foreach $list as $row}}
            {{if $_last_date !== $row['date']}}
              {{$_last_date = $row['date']}}
              <tr class="row-date">
                <th id="date-{{$row.date|escape}}" colspan="9">
                  {{$row.date|as_date:'full'|escape}}
                </th>
              </tr>
              {{registerCss}}
                .row-date {
                  background-color: #444!important;
                  color: #ddd!important;
                }
              {{/registerCss}}
            {{/if}}
            <tr>
              <td>
                {{$_filter = [
                  'lobby' => $row.lobby_key,
                  'rule' => $row.rule_key,
                  'map' => $row.map_key,
                  'weapon' => $row.weapon_key,
                  'term' => 'term',
                  'term_from' => $row.date|cat:' 00:00:00',
                  'term_to' => $row.date|cat:' 23:59:59'
                ]}}
                <a href="{{url route="show/user" screen_name=$user->screen_name filter=$_filter}}">
                  <span class="fa fa-search"></span>
                </a>
              </td>
              <td>{{$row.lobby_name|escape}}</td>
              <td>{{$row.rule_name|escape}}</td>
              <td>{{$row.map_name|escape}}</td>
              <td>{{$row.weapon_name|escape}}</td>
              <td class="text-right">{{$row.battles|number_format|escape}}</td>
              <td class="text-right">
                {{if $row.battles > 0}}{{* 絶対真になるはず *}}
                  {{($row.wins*100/$row.battles)|number_format:1|escape}}%
                {{/if}}
              </td>
              <td class="text-center">
                {{if $row.battles > 0}}{{* 絶対真になるはず *}}
                  {{($row.kill/$row.battles)|number_format:1|escape}}
                  &#32;/&#32;
                  {{($row.death/$row.battles)|number_format:1|escape}}
                {{/if}}
              </td>
              <td class="text-center">
                {{if $row.death > 0}}
                  {{($row.kill/$row.death)|number_format:2|escape}}
                {{elseif $row.kill > 0}}
                  {{99.99|number_format:2|escape}}
                {{else}}
                  -
                {{/if}}
              </td>
            </tr>
          {{foreachelse}}
            <tr>
              <td colspan="9">
                {{'There are no data.'|translate:'app'|escape}}
              </td>
            </tr>
          {{/foreach}}
        </tbody>
      </table>
    </div>
  </div>
{{/strip}}
