{{strip}}
  {{set layout="main.tpl"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  {{use class="yii\helpers\Url"}}
  {{use class="yii\widgets\ListView"}}
  {{$name = '{0}-san'|translate:'app':$user->name}}
  {{$title = "{0}'s SplatLog"|translate:'app':$name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>
    
    {{$battle = $user->latestBattle}}
    {{if $battle && $battle->agent && $battle->agent->isIkaLog}}
      {{if $battle->agent->getIsOldIkalogAsAtTheTime($battle->at)}}
        {{registerCss}}
          .old-ikalog {
            font-weight: bold;
            color: #f00;
          }
        {{/registerCss}}
        <p class="old-ikalog">
          {{'These battles were recorded with an outdated version of IkaLog. Please upgrade to the latest version.'|translate:'app'|escape}}
        </p>
      {{/if}}
    {{/if}}

    {{$_btl = $summary->battle_count}}
    {{if $summary->wp === null}}
      {{$_wp = '-'}}
    {{else}}
      {{$_wp = $summary->wp|string_format:'%.1f%%'}}
    {{/if}}
    {{if $summary->kd_present > 0}}
      {{$_kill = ($summary->total_kill/$summary->kd_present)|string_format:'%.2f'}}
      {{$_death = ($summary->total_death/$summary->kd_present)|string_format:'%.2f'}}
      {{if $summary->total_death == 0}}
        {{if $summary->total_kill == 0}}
          {{$_kr = '-'}}
        {{else}}
          {{$_kr = '∞'}}
        {{/if}}
      {{else}}
        {{$_kr = ($summary->total_kill/$summary->total_death)|string_format:'%.2f'}}
      {{/if}}
    {{else}}
      {{$_kill = '-'}}
      {{$_death = '-'}}
      {{$_kr = '-'}}
    {{/if}}
    {{$_formatted = 'Battles:{0} / Win %:{1} / Avg Killed:{2} / Avg Dead:{3} / Kill Ratio:{4}'|translate:'app':[$_btl,$_wp,$_kill,$_death,$_kr]}}
    {{$_tweet = $title|cat:' [ ':$_formatted:' ]'}}
    {{SnsWidget tweetText=$_tweet}}

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        <div class="text-right">
          {{ListView::widget([
              'dataProvider' => $battleDataProvider,
              'itemView' => 'battle.tablerow.tpl',
              'itemOptions' => [ 'tag' => false ],
              'layout' => '{pager}',
              'pager' => [
                'maxButtonCount' => 5
              ]
            ])}}
        </div>
        <div style="margin-bottom:15px">
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <div class="user-label">
                {{'Summary: Based on the current filter'|translate:'app'|escape}}
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
              <div class="user-label">{{'Battles'|translate:'app'|escape}}</div>
              <div class="user-number">{{$summary->battle_count|number_format|escape}}</div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
              <div class="user-label">{{'Win %'|translate:'app'|escape}}</div>
              <div class="user-number">
                {{if $summary->wp === null}}
                  {{'N/A'|translate:'app'|escape}}
                {{else}}
                  {{$summary->wp|string_format:'%.1f%%'|escape}}
                {{/if}}
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
              <div class="user-label">{{'24H Win %'|translate:'app'|escape}}</div>
              <div class="user-number">
                {{if $summary->wp_short === null}}
                  {{'N/A'|translate:'app'|escape}}
                {{else}}
                  {{$summary->wp_short|string_format:'%.1f%%'|escape}}
                {{/if}}
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
              <div class="user-label">{{'Avg Killed'|translate:'app'|escape}}</div>
              <div class="user-number">
                {{if $summary->kd_present > 0}}
                  {{$p = ['number' => $summary->total_kill, 'battle' => $summary->kd_present]}}
                  {{$t = '{number} killed in {battle, plural, =1{1 battle} other{# battles}}'|translate:'app':$p}}
                  <span class="auto-tooltip" title="{{$t|escape}}">
                    {{($summary->total_kill/$summary->kd_present)|string_format:'%.2f'|escape}}
                  </span>
                {{else}}
                  {{'N/A'|translate:'app'|escape}}
                {{/if}}
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
              <div class="user-label">{{'Avg Dead'|translate:'app'|escape}}</div>
              <div class="user-number">
                {{if $summary->kd_present > 0}}
                  {{$p = ['number' => $summary->total_death, 'battle' => $summary->kd_present]}}
                  {{$t = '{number} dead in {battle, plural, =1{1 battle} other{# battles}}'|translate:'app':$p}}
                  <span class="auto-tooltip" title="{{$t|escape}}">
                    {{($summary->total_death/$summary->kd_present)|string_format:'%.2f'|escape}}
                  </span>
                {{else}}
                  {{'N/A'|translate:'app'|escape}}
                {{/if}}
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
              <div class="user-label">{{'Kill Ratio'|translate:'app'|escape}}</div>
              <div class="user-number">
                {{if $summary->kd_present > 0}}
                  {{if $summary->total_death == 0}}
                    {{if $summary->total_kill == 0}}
                      {{'N/A'|translate:'app'|escape}}
                    {{else}}
                      ∞
                    {{/if}}
                  {{else}}
                    {{($summary->total_kill/$summary->total_death)|string_format:'%.2f'|escape}}
                  {{/if}}
                {{else}}
                  -
                {{/if}}
              </div>
            </div>
          </div>
        </div>
        <div>
          <a href="#filter-form" class="visible-xs-inline btn btn-info">
            <span class="fa fa-search left"></span>{{'Search'|translate:'app'|escape}}
          </a>&#32;
          <a href="#table-config" class="btn btn-default">
            <span class="fa fa-cogs left"></span>{{'View Settings'|translate:'app'|escape}}
          </a>&#32;
          {{$params = array_merge($filter->toQueryParams(), ['v' => 'simple', '0' => 'show/user'])}}
          <a href="{{Url::to($params)|escape}}" class="btn btn-default" rel="nofollow">
            <span class="fa fa-list left"></span>{{'Simplified List'|translate:'app'|escape}}
          </a>
        </div>
        <div class="table-responsive" id="battles">
          <table class="table table-striped table-condensed">
            <thead>
              <tr>
                <th></th>
                <th class="cell-lobby">{{'Lobby'|translate:'app'|escape}}</th>
                <th class="cell-rule">{{'Mode'|translate:'app'|escape}}</th>
                <th class="cell-map">{{'Map'|translate:'app'|escape}}</th>
                <th class="cell-main-weapon">{{'Weapon'|translate:'app'|escape}}</th>
                <th class="cell-sub-weapon">{{'Sub Weapon'|translate:'app'|escape}}</th>
                <th class="cell-special">{{'Special'|translate:'app'|escape}}</th>
                <th class="cell-rank">{{'Rank'|translate:'app'|escape}}</th>
                <th class="cell-rank-after">{{'Rank (After)'|translate:'app'|escape}}</th>
                <th class="cell-level">{{'Level'|translate:'app'|escape}}</th>
                {{* <th class="cell-level-after">{{'Level (After)'|translate:'app'|escape}}</th> *}}
                <th class="cell-result">{{'Result'|translate:'app'|escape}}</th>
                <th class="cell-kd">{{'k'|translate:'app'|escape}}/{{'d'|translate:'app'|escape}}</th>
                <th class="cell-kill-ratio">{{'KR'|translate:'app'|escape}}</th>
                <th class="cell-point">{{'Point'|translate:'app'|escape}}</th>
                <th class="cell-rank-in-team">{{'Rank in Team'|translate:'app'|escape}}</th>
                <th class="cell-datetime">{{'Date Time'|translate:'app'|escape}}</th>
                <th class="cell-reltime">{{'Relative Time'|translate:'app'|escape}}</th>
              </tr>
            </thead>
            <tbody>
              {{ListView::widget([
                'dataProvider' => $battleDataProvider,
                'itemView' => 'battle.tablerow.tpl',
                'itemOptions' => [ 'tag' => false ],
                'layout' => '{items}'
              ])}}
            </tbody>
          </table>
        </div>
        <div class="text-right">
          {{ListView::widget([
              'dataProvider' => $battleDataProvider,
              'itemView' => 'battle.tablerow.tpl',
              'itemOptions' => [ 'tag' => false ],
              'layout' => '{pager}',
              'pager' => [
                'maxButtonCount' => 5
              ]
            ])}}
        </div>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        <div style="border:1px solid #ccc;border-radius:5px;padding:15px;margin-bottom:15px;">
          {{$terms = [
              ''            => 'Any Time'|translate:'app',
              'this-period' => 'Current Period'|translate:'app',
              'last-period' => 'Previous Period'|translate:'app',
              '24h'         => 'Last 24 Hours'|translate:'app',
              'today'       => 'Today'|translate:'app',
              'yesterday'   => 'Yesterday'|translate:'app',
              'term'        => 'Specify Period'|translate:'app'
            ]}}
          {{ActiveForm assign="_" id="filter-form" action=['show/user', 'screen_name' => $user->screen_name] method="get"}}
            {{$_->field($filter, 'lobby')->dropDownList($lobbies)->label(false)}}
            {{$_->field($filter, 'rule')->dropDownList($rules)->label(false)}}
            {{$_->field($filter, 'map')->dropDownList($maps)->label(false)}}
            {{$_->field($filter, 'weapon')->dropDownList($weapons)->label(false)}}
            {{$_->field($filter, 'result')->dropDownList($results)->label(false)}}
            {{$_->field($filter, 'term')->dropDownList($terms)->label(false)}}
            <div id="filter-term-group">
              {{$_->field($filter, 'term_from', [
                  'inputTemplate' => '<div class="input-group"><span class="input-group-addon">From:</span>{input}</div>'|translate:'app'
                ])->input('text', ['placeholder' => 'YYYY-MM-DD hh:mm:ss'])->label(false)}}
              {{$_->field($filter, 'term_to', [
                  'inputTemplate' => '<div class="input-group"><span class="input-group-addon">To:</span>{input}</div>'|translate:'app'
                ])->input('text', ['placeholder' => 'YYYY-MM-DD hh:mm:ss'])->label(false)}}

              {{\jp3cki\yii2\datetimepicker\BootstrapDateTimePickerAsset::register($this)|@void}}
              {{registerCss}}#filter-term-group{margin-left:5%}{{/registerCss}}
              {{registerJs}}
                (function($) {
                  $('#filter-term-group input').datetimepicker({
                    format: "YYYY-MM-DD HH:mm:ss"
                  });
                  $('#filter-term').change(function() {
                    if ($(this).val() === 'term') {
                      $('#filter-term-group').show();
                    } else {
                      $('#filter-term-group').hide();
                    }
                  }).change();
                })(jQuery);
              {{/registerJs}}
            </div>
{{*
            TODO:k/d<br>
*}}
            <input type="submit" value="{{'Search'|translate:'app'|escape}}" class="btn btn-primary">
          {{/ActiveForm}}
        </div>
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{AdWidget}}
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="table-config">
        <div>
          <label>
            <input type="checkbox" id="table-hscroll" value="1"> {{'Always enable horizontal scroll'|translate:'app'|escape}}
          <label>
        </div>
        <div class="row">
          <div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-lobby"> {{'Lobby'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-rule"> {{'Mode'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-map"> {{'Map'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-main-weapon"> {{'Weapon'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-sub-weapon"> {{'Sub Weapon'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-special"> {{'Special'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-rank"> {{'Rank'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-rank-after"> {{'Rank (After)'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-level"> {{'Level'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
        {{*
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-level-after"> {{'Level (After)'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
        *}}
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-result"> {{'Result'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-kd"> {{'k'|translate:'app'|escape}}/{{'d'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-kill-ratio"> {{'Kill Ratio'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-point"> {{'Turf Inked'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-rank-in-team"> {{'Rank in Team'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-datetime"> {{'Date Time'|translate:'app'|escape}}</label>
          </div><div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
            <label><input type="checkbox" class="table-config-chk" data-klass="cell-reltime"> {{'Relative Time'|translate:'app'|escape}}</label>
          </div>
        </div>
      </div>
    </div>
  </div>
{{/strip}}
{{registerJs}}window.battleList();{{/registerJs}}
{{registerJs}}window.battleListConfig();{{/registerJs}}
