{{strip}}
  {{set layout="main.tpl"}}

  {{$title = "{0}'s Battle Stats (Cause of Death)"|translate:'app':$user->name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->userIcon->absUrl|default:$user->jdenticonPngUrl])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}

  {{\jp3cki\yii2\flot\FlotPieAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{SnsWidget}}

    <p>
      {{$_groups = [
          ''            => 'Don\'t group',
          'canonical'   => 'Group by reskins',
          'main-weapon' => 'Group by main weapon',
          'type'        => 'Group by weapon type'
        ]}}
      {{if $group->hasErrors()}}
        {{$_selected = ''}}
      {{else}}
        {{$_selected = $group->level}}
      {{/if}}
      {{foreach $_groups as $_k => $_v}}
        {{if !$_v@first}}
          &#32;|&#32;
        {{/if}}
        {{if $_k != $_selected}}
          {{$_param_group = ['level' => $_k]}}
          <a href="{{url route='show/user-stat-cause-of-death' screen_name=$user->screen_name filter=$filter->attributes group=$_param_group}}">
            {{$_v|translate:'app'|escape}}
          </a>
        {{else}}
          {{$_v|translate:'app'|escape}}
        {{/if}}
      {{/foreach}}
    </p>

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        <table class="table table-striped">
          <tbody>
            {{$total = 0}}
            {{foreach $list as $row}}
              {{$total = $total + $row->count}}
            {{/foreach}}

            {{$rank = 0}}
            {{$last = null}}
            {{foreach $list as $i => $row}}
              <tr class="cause-of-death" data-name="{{$row->name|escape}}" data-count="{{$row->count|escape}}">
                <td class="text-right">
                  {{if $last !== $row->count}}
                    {{$rank = $i + 1}}
                    {{$last = $row->count}}
                  {{/if}}
                  {{$rank|escape}}
                </td>
                <td>
                  {{$row->name|escape}}
                </td>
                <td class="text-right">
                  {{$params = [
                      'nFormatted' => $row->count|number_format,
                      'n' => $row->count
                    ]}}
                  {{'{nFormatted} {n, plural, =1{time} other{times}}'|translate:'app':$params}}
                </td>
                <td class="text-right">
                  {{($row->count*100/$total)|number_format:2|escape}}%
                </td>
              </tr>
            {{foreachelse}}
              <tr>
                <td>{{'There are no data.'|translate:'app'|escape}}</td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{BattleFilterWidget route="show/user-stat-cause-of-death" screen_name=$user->screen_name filter=$filter action="summarize"}}
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
