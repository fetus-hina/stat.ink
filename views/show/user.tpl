{{strip}}
  {{set layout="main.tpl"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  {{use class="yii\widgets\ListView"}}
  <div class="container">
    <h1>
      {{$name = '{0}-san'|translate:'app':$user->name}}
      {{$title = "{0}'s Log"|translate:'app':$name}}
      {{$title|escape}}
      {{set title="{{$app->name}} | {{$title}}"}}
    </h1>
    <h2>
      {{'Recent Results'|translate:'app'|escape}}
    </h2>
    <p>
      Coming soon
    </p>

    <h2>
      {{'Battles'|translate:'app'|escape}}
    </h2>
    <div class="row">
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 pull-right" style="padding:15px">
        <div style="border:1px solid #ccc;border-radius:5px;padding:15px">
          {{ActiveForm assign="_" id="filter-form" action=['show/user', 'screen_name' => $user->screen_name] method="get"}}
            {{$_->field($filter, 'rule')->dropDownList($rules)->label(false)}}
            {{$_->field($filter, 'map')->dropDownList($maps)->label(false)}}
            {{$_->field($filter, 'weapon')->dropDownList($weapons)->label(false)}}
            {{$_->field($filter, 'result')->dropDownList($results)->label(false)}}
{{*
            TODO:k/d<br>
            TODO:期間<br>
*}}
            <input type="submit" value="{{'Search'|translate:'app'|escape}}" class="btn btn-primary">
          {{/ActiveForm}}
        </div>
      </div>
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9" id="battles">
        {{ListView::widget([
            'dataProvider' => $battleDataProvider,
            'itemView' => 'battle.tablerow.tpl',
            'itemOptions' => [ 'tag' => false ],
            'layout' => '{summary}{pager}'
          ])}}
        <table class="table table-striped">
          <thead>
            <tr>
              <th></th>
              <th>{{'Rule'|translate:'app'|escape}}</th>
              <th>{{'Map'|translate:'app'|escape}}</th>
              <th>{{'Weapon'|translate:'app'|escape}}</th>
              <th>{{'Result'|translate:'app'|escape}}</th>
              <th>{{'k'|translate:'app'|escape}}</th>
              <th>{{'d'|translate:'app'|escape}}</th>
              <th>{{'Date Time'|translate:'app'|escape}}</th>
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
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 pull-right" style="padding:15px">
        <div style="border:1px solid #ccc;border-radius:5px;padding:15px">
          <h2 style="margin-top:0;margin-bottom:10px">
            <a href="{{url route="show/user" screen_name=$user->screen_name}}">
              {{'{0}-san'|translate:'app':$user->name|escape}}
            </a>
          </h2>
          <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              {{$stat = $user->simpleStatics}}
              <div class="user-label">
                {{'Battles'|translate:'app'|escape}}
              </div>
              <div class="user-number">
                <a href="{{url route="show/user" screen_name=$user->screen_name}}">
                  {{$stat->totalBattleCount|number_format|escape}}
                </a>
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <div class="user-label">
                {{'WP'|translate:'app'|escape}}
              </div>
              <div class="user-number">
                {{if $stat->totalWinRate === null}}
                  {{'N/A'|translate:'app'|escape}}
                {{else}}
                  {{$stat->totalWinRate|string_format:'%.1f%%'|escape}}
                {{/if}}
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <div class="user-label">
                {{'24H WP'|translate:'app'|escape}}
              </div>
              <div class="user-number">
                {{if $stat->oneDayWinRate === null}}
                  {{'N/A'|translate:'app'|escape}}
                {{else}}
                  {{$stat->oneDayWinRate|string_format:'%.1f%%'|escape}}
                {{/if}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{/strip}}
{{registerCss}}{{literal}}
.user-label{color:#aaa}
.user-number{font-size:1.5em}
{{/literal}}{{/registerCss}}
