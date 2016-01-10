{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Profile and Settings'|translate:'app'}}"}}
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
        <h1>
          {{'Profile and Settings'|translate:'app'|escape}}

          <a href="{{url route="user/edit-profile"}}" class="btn btn-primary" style="margin-left:30px">
            {{'Update'|translate:'app'|escape}}
          </a>
        </h1>

        <table class="table table-striped">
          <tbody>
            <tr>
              <th>{{'Internal ID'|translate:'app'|escape}}</th>
              <td>#{{$user->id|escape}}</td>
            </tr>
            <tr>
              <th>{{'Screen Name'|translate:'app'|escape}}</th>
              <td><code>{{$user->screen_name|escape}}</code></td>
            </tr>
            <tr>
              <th>{{'Password'|translate:'app'|escape}}</th>
              <td>
                <code>**********</code> <a href="{{url route="user/edit-password"}}" class="btn btn-default">{{'Change Password'|translate:'app'|escape}}</a>
              </td>
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
            <tr>
              <th>{{'User Name'|translate:'app'|escape}}</th>
              <td>{{$user->name|escape}}</td>
            </tr>
            <tr>
              <th>{{'Black out other players'|translate:'app'|escape}}</th>
              <td>
                {{if $user->is_black_out_others}}
                  {{'Yes'|translate:'app'|escape}}
                {{else}}
                  {{'No'|translate:'app'|escape}}
                {{/if}}
              </td>
            </tr>
            <tr>
              <th>{{'Nintendo Network ID'|translate:'app'|escape}}</th>
              <td>
                {{if $user->nnid != ''}}
                  <a href="https://miiverse.nintendo.net/users/{{$user->nnid|escape:url}}">
                    {{$user->nnid|escape}}
                  </a>
                {{else}}
                  -
                {{/if}}
              </td>
            </tr>
            <tr>
              <th>{{'Twitter @name'|translate:'app'|escape}}</th>
              <td>
                {{if $user->twitter != ''}}
                  <a href="https://twitter.com/{{$user->twitter|escape:url}}">
                    <span class="fa fa-twitter left"></span>@{{$user->twitter|escape}}
                  </a>
                {{else}}
                  -
                {{/if}}
              </td>
            </tr>
            <tr>
              <th>{{'IKANAKAMA User ID'|translate:'app'|escape}}</th>
              <td>
                {{if $user->ikanakama != ''}}
                  <a href="http://ikazok.net/users/{{$user->ikanakama|escape:url}}">
                    #{{$user->ikanakama|escape}}
                  </a>
                {{else}}
                  -
                {{/if}}
              </td>
            </tr>
            <tr>
              <th>{{'Capture Environment'|translate:'app'|escape}}</th>
              <td>
                {{if $user->env}}
                  {{$user->env->text|escape|nl2br}}
                {{else}}
                  -
                {{/if}}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <h2>{{'Export'|translate:'app'|escape}}</h2>
        <p>
          <a href="{{url route="/user/download" type="ikalog-csv"}}" class="btn btn-default btn-block">
            <span class="fa fa-file-excel-o left"></span>
            {{'CSV (IkaLog compat.)'|translate:'app'|escape}}
          </a>
          <a href="{{url route="/user/download" type="ikalog-json"}}" class="btn btn-default btn-block">
            <span class="fa fa-file-code-o left"></span>
            {{'JSON (IkaLog compat.)'|translate:'app'|escape}}
          </a>
        </p>
      </div>
    </div>
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
