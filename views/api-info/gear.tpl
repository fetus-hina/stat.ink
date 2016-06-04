{{strip}}
  {{set layout="main.tpl"}}

  {{$_type = $type->name|translate:'app-gear'}}
  {{$title = 'API Info: Gears: {0}'|translate:'app':$_type|escape}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    {{\app\assets\JqueryStupidTableAsset::register($this)|@void}}
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
    <p>
      {{foreach $types as $_type}}
        {{if !$_type@first}}
          &#32;|&#32;
        {{/if}}
        {{if $_type.key !== $type.key}}
          <a href="gear-{{$_type.key|escape:url}}">
        {{/if}}
        {{$_type.name|translate:'app-gear'|escape}}
        {{if $_type.key !== $type.key}}
          </a>
        {{/if}}
      {{/foreach}}
    </p>
    <div class="table-responsive table-responsive-force">
      <table class="table table-striped table-condensed table-sortable">
        <thead>
          <tr>
            <th data-sort="string">
              {{'Brand'|translate:'app'|escape}}
            </th>
            <th data-sort="string">
              {{'Primary Ability'|translate:'app'|escape}}
            </th>
            <th data-sort="string">
              <code>key</code>
            </th>
            {{foreach $langs as $lang}}
              <th data-sort="string">
                {{$lang.name|escape}}
              </th>
            {{/foreach}}
          </tr>
        </thead>
        <tbody>
          {{foreach $gears as $_gear}}
            <tr>  
              <td data-sort-value="{{$_gear.brand|escape}}">
                {{$_gear.brand|escape}}
              </td>
              <td data-sort-value="{{$_gear.ability|escape}}">
                {{$_gear.ability|escape}}
              </td>
              <td data-sort-value="{{$_gear.key|escape}}">
                <code>{{$_gear.key|escape}}</code>
              </td>
              {{foreach $langs as $lang}}
                <td>
                  {{$langKey = $lang.lang|replace:'-':'_'}}
                  {{$_gear.names[$langKey]|escape}}
                </td>
              {{/foreach}}
            </tr>
          {{/foreach}}
        </tbody>
      </table>
    </div>
    <hr>
    <p>
      <img src="/static-assets/cc/cc-by.svg" alt="CC-BY 4.0"><br>
      {{'This document is under a <a href="http://creativecommons.org/licenses/by/4.0/deed.en">Creative Commons Attribution 4.0 International License</a>.'|translate:'app'}}
    </p>
  </div>
{{/strip}}
