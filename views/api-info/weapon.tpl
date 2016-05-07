{{strip}}
  {{set layout="main.tpl"}}

  {{$title = 'API Info: Weapons'|translate:'app'|escape}}

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
    <div class="table-responsive table-responsive-force">
      <table class="table table-striped table-condensed table-sortable">
        <thead>
          <tr>
            <th data-sort="int">
              {{'Category'|translate:'app'|escape}}
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
          {{foreach $types as $type}}
            {{foreach $type.weapons as $weapon}}
              <tr>  
                <td data-sort-value="{{$type@index|escape}}">
                  {{$type.name|escape}}
                </td>
                <td data-sort-value="{{$weapon.key|escape}}">
                  <code>{{$weapon.key|escape}}</code>
                </td>
                {{foreach $langs as $lang}}
                  <td>
                    {{$langKey = $lang.lang|replace:'-':'_'}}
                    {{$weapon.names[$langKey]|escape}}
                  </td>
                {{/foreach}}
              </tr>
            {{/foreach}}
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
