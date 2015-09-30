{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Open Source Licenses'|translate:'app'}}"}}
  <div class="container">
    <h1 class="ikamodoki">{{'Open Source Licenses'|translate:'app'|escape}}</h1>
    <div>
      <h2 class="ikamodoki">{{$myself->name|escape}}</h2>
      <div class="license-body">
        {{$myself->html}}
      </div>
    </div>
    <hr>
    {{foreach $depends as $software}}
      <div>
        <h2 class="ikamodoki">{{$software->name|escape}}</h2>
        <div class="license-body">
          {{$software->html}}
        </div>
      </div>
    {{/foreach}}
  </div>
{{/strip}}
