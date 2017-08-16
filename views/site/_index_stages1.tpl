{{strip}}
  {{use class="app\models\PeriodMap"}}
  {{$stageInfo = PeriodMap::getSchedule()}}
  {{if $stageInfo->current->regular || $stageInfo->current->gachi}}
    {{\app\assets\MapImageAsset::register($this)|@void}}
    {{$timeFormat = '%H:%M'}}
    {{if $app->language == 'en-US'}}
      {{$timeFormat = '%l:%M %p'}}
    {{/if}}
    <h2>
      <span class="hidden-xs">{{'Current Stages'|translate:'app'|escape}}</span>
      {{if $stageInfo->current->t}}
        {{$t = $stageInfo->current->t}}
        <span class="hidden-xs">&#32;[</span>
        {{$t.0|date_format:$timeFormat|escape}}-{{$t.1|date_format:$timeFormat|escape}}
        <span class="hidden-xs">]</span>
      {{/if}}
      {{if $stageInfo->next->regular || $stageInfo->next->gachi}}
        &#32;<button id="show-next-stage" type="button" class="btn btn-default">
          {{if $stageInfo->next->t}}
            {{$t = $stageInfo->next->t}}
            {{$t.0|date_format:$timeFormat|escape}}-{{$t.1|date_format:$timeFormat|escape}}
          {{else}}
            {{'Next Stage'|translate:'app'|escape}}
          {{/if}}
        </button>
        {{registerJs}}
          $('#show-next-stage').click(function(){
            var $this = $(this);
            var $next = $('#next-stage');
            $.smoothScroll({
              offset: -60,
              scrollTarget: $next,
              beforeScroll: function () {
                $next.show('fast');
                $this.hide();
              }
            });
            return false;
          });
        {{/registerJs}}
      {{/if}}
    </h2>
    <p class="text-right" style="margin:0">
      <!--a href="http://graystar0907.wixsite.com/bukiicons" rel="external"-->
        {{"Weapon icons were created by {0}."|translate:'app':'Stylecase'|escape}}
      <!--/a-->
    </p>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
        {{include
            file="@app/views/site/_index_stage.tpl"
            rule=$stageInfo->current->regular.0->rule
            stages=$stageInfo->current->regular
        }}
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
        {{include
            file="@app/views/site/_index_stage.tpl"
            rule=$stageInfo->current->gachi.0->rule
            stages=$stageInfo->current->gachi
        }}
      </div>
    </div>
    {{if $stageInfo->next->regular && $stageInfo->next->gachi}}
      {{registerCss}}
        #next-stage{display:none}
      {{/registerCss}}
      <div id="next-stage">
        <h2>
          <span class="hidden-xs">{{'Next Stages'|translate:'app'|escape}}</span>
          {{if $stageInfo->next->t}}
            {{$t = $stageInfo->next->t}}
            <span class="hidden-xs">&#32;[</span>
            {{$t.0|date_format:$timeFormat|escape}}-{{$t.1|date_format:$timeFormat|escape}}
            <span class="hidden-xs">]</span>
          {{/if}}
        </h2>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            {{include
                file="@app/views/site/_index_stage.tpl"
                rule=$stageInfo->next->regular.0->rule
                stages=$stageInfo->next->regular
            }}
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            {{include
                file="@app/views/site/_index_stage.tpl"
                rule=$stageInfo->next->gachi.0->rule
                stages=$stageInfo->next->gachi
            }}
          </div>
        </div>
      </div>
    {{/if}}
  {{/if}}
{{/strip}}
