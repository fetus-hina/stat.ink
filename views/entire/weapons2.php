<?php
use app\assets\AppOptAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotStackAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\helpers\Html;
use yii\helpers\Json;

$title = Yii::t('app', 'Weapons');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

FlotAsset::register($this);
FlotTimeAsset::register($this);
FlotStackAsset::register($this);

$asset = AppOptAsset::register($this);
$asset->registerJsFile($this, 'weapons.js');

$this->registerCss('.graph{height:300px}');
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>  
  <?= SnsWidget::widget() . "\n" ?>
  <ul class="nav nav-tabs">
    <li class="active"><a href="javascript:;">Splatoon 2</a></li>
    <li><?= Html::a('Splatoon', ['entire/weapons']) ?></li>
  </ul>
  <h2>
    <?= Html::encode(Yii::t('app', 'Weapons')) . "\n" ?>
  </h2>
  <p>
    <?= Html::encode(
      Yii::t(
        'app',
        'Excluded: The uploader, All players (Private Battle), Uploader\'s teammates (Squad Battle or Splatfest Battle)'
      )
    ) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(
      Yii::t('app', '* This exclusion is in attempt to minimize overcounting in weapon usage statistics.')
    ) . "\n" ?>
  </p>
<?php /*
  {{\app\assets\JqueryStupidTableAsset::register($this)|@void}}
  {{foreach $entire as $rule}}
    {{if !$rule@first}} | {{/if}}
    <a href="#weapon-{{$rule->key|escape}}">{{$rule->name|escape}}</a>
  {{/foreach}}
*/ ?>
  <h3 id="trends">
    <?= Html::encode(Yii::t('app', 'Trends')) . "\n" ?>
  </h3>
  <p>
    <?= Html::a(
      implode(' ', [
        Html::tag('span', '', ['class' => 'fa fa-exchange fa-fw']),
        Html::encode(Yii::t('app', 'Compare number of uses')),
      ]),
      ['entire/weapons2-use'],
      ['class' => 'btn btn-default', 'disabled' => true]
    ) . "\n" ?>
  </p>
  <div id="graph-trends-legends"></div>
  <?= Html::tag('div', '', [
    'id' => 'graph-trends',
    'class' => 'graph',
    'data' => [
      'label-others' => Yii::t('app', 'Others'),
    ],
  ]) . "\n" ?>
  <p class="text-right">
    <label>
      <input type="checkbox" id="stack-trends" value="1" checked>
      <?= Html::encode(Yii::t('app', 'Stack')) . "\n" ?>
    </label>
  </p>
  <?= Html::tag(
    'script',
    Json::encode($uses),
    ['id' => 'trends-json', 'type' => 'application/json']
  ) . "\n" ?>
<?php /*
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
            <th data-sort="float">{{'Avg Kills'|translate:'app'|escape}}</th>
            <th data-sort="float">{{'Avg Deaths'|translate:'app'|escape}}</th>
            <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
            {{if $rule->key === 'nawabari'}}
              <th data-sort="float">{{'Avg Inked'|translate:'app'|escape}}</th>
            {{/if}}
            <th data-sort="float">{{'Win %'|translate:'app'|escape}}</th>
          </tr>
        </thead>
        <tbody>
          {{foreach $rule->data->weapons as $weapon}}
            <tr class="weapon">
              <td>
                <a href="{{url route="entire/weapon" weapon=$weapon->key rule=$rule->key}}">
                  <span title="{{'Sub:'|translate:'app'|escape}}{{$weapon->subweapon->name|escape}} / {{'Special:'|translate:'app'|escape}}{{$weapon->special->name|escape}}" class="auto-tooltip">
                    {{$weapon->name|escape}}
                  </span>
                </a>
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
            <th data-sort="float">{{'Avg Kills'|translate:'app'|escape}}</th>
            <th data-sort="float">{{'Avg Deaths'|translate:'app'|escape}}</th>
            <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
            <th data-sort="float">{{'Win %'|translate:'app'|escape}}</th>
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
            <th data-sort="float">{{'Avg Kills'|translate:'app'|escape}}</th>
            <th data-sort="float">{{'Avg Deaths'|translate:'app'|escape}}</th>
            <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
            <th data-sort="float">{{'Win %'|translate:'app'|escape}}</th>
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
*/ ?>
</div>
