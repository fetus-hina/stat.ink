{{strip}}
  {{\app\assets\ZxcvbnAsset::register($this)|@void}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Profile and Settings'|translate:'app'}}"}}
  <div class="container">
    <h1>{{'Profile and Settings'|translate:'app'|escape}}</h1>

    <table class="table table-striped">
      <tbody>
        <tr>
          <th>{{'Internal ID'|translate:'app'|escape}}</th>
          <td>#{{$user->id|escape}}</td>
        </tr>
        <tr>
          <th>{{'User Name'|translate:'app'|escape}}</th>
          <td>{{$user->name|escape}}</td>
        </tr>
        <tr>
          <th>{{'Screen Name'|translate:'app'|escape}}</th>
          <td><code>{{$user->screen_name|escape}}</code></td>
        </tr>
        <tr>
          <th>{{'Password'|translate:'app'|escape}}</th>
          <td>********</td>
        </tr>
        <tr>
          <th>{{'API Token'|translate:'app'|escape}}</th>
          <td>
            <button class="btn btn-default auto-tooltip" title="{{'Show your API Token'|translate:'app'|escape}}" id="apikey-button">
              <span class="fa fa-eye"></span>
            </button>
            <code id="apikey" style="display:none">{{$user->api_key|escape}}</code>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
{{/strip}}
{{registerCss}}
  th { width: 10em; }
{{/registerCss}}
{{registerJs}}
  "use strict";
  $('#apikey-button').click(function () {
    $(this).hide();
    $('#apikey').show();
  });
{{/registerJs}}
