{{strip}}
  {{set layout="main.tpl"}}
  {{$_date = $month->format('Y-m')}}
  {{$title = 'Stages'|translate:'app'|cat:' - ':$_date}}
  {{set title="{{$app->name}} | {{$title}}"}}

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

    {{if $nextUrl || $prevUrl}}
      <div class="row" style="margin-bottom:15px">
        {{if $prevUrl}}
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <a href="{{$prevUrl|escape}}" class="btn btn-default">
              <span class="fa fa-angle-double-left left"></span>{{'Prev. Month'|translate:'app'|escape}}
            </a>
          </div>
        {{/if}}
        {{if $nextUrl}}
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pull-right text-right">
            <a href="{{$nextUrl|escape}}" class="btn btn-default">
              {{'Next Month'|translate:'app'|escape}}<span class="fa fa-angle-double-right right"></span>
            </a>
          </div>
        {{/if}}
      </div>
    {{/if}}

    <div class="row">
      {{foreach $rules as $__rule}}
        {{$_rule = $__rule->rule}}
        {{$_max = -1}}
        <div class="col-xs-12 col-sm-6 col-lg-3" id="{{$_rule->key|escape}}">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>{{$_rule->name|translate:'app-rule'|escape}}</th>
                <th>{{'Times'|translate:'app'|escape}}</th>
              </tr>
            </thead>
            <tbody>
              {{$_total = 0}}
              {{foreach $__rule->maps as $__map}}
                {{$_map = $__map->map}}
                {{$_count = $__map->count}}
                {{$_total = $_total + $_count}}
                {{if $_count > $_max}}
                  {{$_max = $_count}}
                {{/if}}
                <tr class="{{if $_count == $_max}}max{{/if}}">
                  <td>
                    <a href="{{url route="stage/map" map=$_map->key}}#{{$_rule->key|escape}}">
                      {{$_map->name|translate:'app-map'|escape}}
                    </a>
                  </td>
                  <td class="text-right">
                    {{$_count|number_format}}
                  </td>
                </tr>
              {{/foreach}}
              <tr>
                <td>{{'Total'|translate:'app'|escape}}</td>
                <td class="text-right">
                  {{$_total|number_format|escape}}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      {{/foreach}}
    </div>
  </div>
  {{registerCss}}
    tr.max>td{font-weight:bold}
  {{/registerCss}}
{{/strip}}
