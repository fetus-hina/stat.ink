{{strip}}
  {{\app\assets\BattleInputAsset::register($this)|@void}}

  {{$_prefix = 'input-modal2-internal'|sha1|substr:0:8}}

  {{$_agentName = $app->name|cat:' web client'}}
  {{$_agentVersion = 'v'|cat:$app->version}}
  {{$_agentRevision = \app\components\Version::getShortRevision()}}

  <div class="modal fade" id="inputModal2" tabindex="-1" role="dialog" aria-labelledby="inputModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="{{'Close'|translate:'app'|escape}}">
            <span aria-hidden="true"><span class="fas fa-times"></span>
          </button>
          <h4 class="modal-title" id="inputModalLabel">
            {{'Input new battle results'|translate:'app'|escape}}&#32;
            (α)
            <span class="hidden-xs" style="font-weight:normal" aria-hidden="true">
              &#32;
              <span class="next-stages-will-arrive-in-2">
                <span class="next-stages-will-arrive-in-2--value">-:--:--</span>
              </span>
            </span>
          </h4>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs" role="tablist" style="margin-bottom:15px">
            <li role="presentation" class="active">
              <a href="#_{{$_prefix|escape}}_regular" data-toggle="tab">
                {{'Regular'|translate:'app-rule2'|escape}}
              </a>
            </li>
            <li role="presentation" class="">
              <a href="#_{{$_prefix|escape}}_ranked" data-toggle="tab">
                {{'Ranked'|translate:'app-rule2'|escape}} / {{'League'|translate:'app-rule2'|escape}}
              </a>
            </li>
            <li role="presentation" class="">
              <a href="#_{{$_prefix|escape}}_fest" data-toggle="tab">
                {{'Splatfest'|translate:'app-rule2'|escape}}
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="_{{$_prefix|escape}}_regular">
              <form class="battle-input-form" id="battle-input2-form--regular" action="#" onsubmit="return !1" data-apikey="{{$app->user->identity->api_key|escape}}">
                <input type="hidden" name="agent" value="{{$_agentName|escape}}">
                <input type="hidden" name="agent_version" value="{{$_agentVersion|escape}}" data-version="{{$_agentVersion|escape}}" data-revision="{{$_agentRevision|escape}}">

                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group">
                      <input type="hidden" id="battle-input2-form--regular--rule" name="rule" value="nawabari">
                      <input type="hidden" id="battle-input2-form--regular--mode" name="mode" value="regular">
                      <input type="text" id="battle-input2-form--regular--rule--label" value="{{'Turf War'|translate:'app-rule2'|escape}}" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group">
                      <select id="battle-input2-form--regular--lobby" name="lobby" class="form-control">
                        <option value="standard">
                          {{'Solo Queue'|translate:'app-rule2'|escape}}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!--h5>{{'Weapon'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <select class="form-control battle-input2-form--weapons" id="battle-input2-form--regular--weapon" name="weapon">
                  </select>
                </div>

                <!--h5>{{'Stages'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <select class="form-control" id="battle-input2-form--regular--stage" name="stage">
                    {{use class="app\models\Map2"}}
                    {{foreach Map2::getSortedMap() as $_key => $_name}}
                      <option value="{{$_key|escape}}">
                        {{$_name|escape}}
                      </option>
                    {{/foreach}}
                  </select>
                </div>

                <!--h5>{{'Result'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <input type="hidden" id="battle-input2-form--regular--result" name="result" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--regular--result" data-value="win">
                        {{'Win'|translate:'app'|escape}}
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--regular--result" data-value="lose">
                        {{'Lose'|translate:'app'|escape}}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                      <label for="battle-input2-form--regular--point">
                        {{'Turf inked (including bonus)'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--regular--point" name="my_point" min="0" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input2-form--regular--kill-or-assist">
                        {{'Kill or Assist'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--regular--kill-or-assist" name="kill_or_assist" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input2-form--regular--special">
                        {{'Specials'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--regular--special" name="special" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                </div>

                <div class="form-group text-right">
                  <span class="visible-xs-inline" aria-hidden="true">
                    <span class="next-stages-will-arrive-in-2">
                      <span class="next-stages-will-arrive-in-2--value">-:--:--</span>
                    </span>
                    &#32;
                  </span>
                  <input type="hidden" id="battle-input2-form--regular--uuid" name="uuid" value="">
                  <input type="hidden" id="battle-input2-form--regular--end_at" name="end_at" value="">
                  <button type="button" class="btn btn-primary" id="battle-input2-form--regular--submit" data-form="_{{$_prefix|escape}}_regular" disabled>
                    <span class="far fa-fw fa-save"></span>{{'Save!'|translate:'app'|escape}}
                  </button>
                </div>
              </form>
            </div><!-- panel -->
            <div role="tabpanel" class="tab-pane" id="_{{$_prefix|escape}}_ranked">
              <form class="battle-input-form" id="battle-input2-form--ranked" action="#" onsubmit="return !1" data-apikey="{{$app->user->identity->api_key|escape}}">
                <input type="hidden" name="agent" value="{{$_agentName|escape}}">
                <input type="hidden" name="agent_version" value="{{$_agentVersion|escape}}" data-version="{{$_agentVersion|escape}}" data-revision="{{$_agentRevision|escape}}">

                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group">
                      <select id="battle-input2-form--ranked--rule" name="rule" class="form-control">
                        <option value="area">{{'Splat Zones'|translate:'app-rule2'|escape}}</option>
                        <option value="yagura">{{'Tower Control'|translate:'app-rule2'|escape}}</option>
                        <option value="hoko">{{'Rainmaker'|translate:'app-rule2'|escape}}</option>
                        <option value="asari">{{'Clam Blitz'|translate:'app-rule2'|escape}}</option>
                      </select>
                      <input type="hidden" id="battle-input2-form--ranked--mode" name="mode" value="gachi">
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group">
                      <select id="battle-input2-form--ranked--lobby" name="lobby" class="form-control">
                        <option value="standard">
                          {{'Ranked Battle (Solo)'|translate:'app-rule2'|escape}}
                        </option>
                        <option value="squad_2">
                          {{'League Battle (Twin)'|translate:'app-rule2'|escape}}
                        </option>
                        <option value="squad_4">
                          {{'League Battle (Quad)'|translate:'app-rule2'|escape}}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!--h5>{{'Weapon'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <select class="form-control battle-input2-form--weapons" id="battle-input2-form--ranked--weapon" name="weapon">
                  </select>
                </div>

                <!--h5>{{'Stages'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <select class="form-control" id="battle-input2-form--ranked--stage" name="stage">
                    {{foreach Map2::getSortedMap() as $_key => $_name}}
                      <option value="{{$_key|escape}}">
                        {{$_name|escape}}
                      </option>
                    {{/foreach}}
                  </select>
                </div>

                <!--h5>{{'Result'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <input type="hidden" id="battle-input2-form--ranked--result" name="result" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--ranked--result" data-value="win">
                        {{'Win'|translate:'app'|escape}}
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--ranked--result" data-value="lose">
                        {{'Lose'|translate:'app'|escape}}
                      </button>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <input type="hidden" id="battle-input2-form--ranked--knock_out" name="knock_out" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--knock_out" data-target="battle-input2-form--ranked--knock_out" data-value="yes">
                        {{'Knockout'|translate:'app'|escape}}
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--knock_out" data-target="battle-input2-form--ranked--knock_out" data-value="no">
                        {{'Time is up'|translate:'app'|escape}}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-12 col-sm-6 form-inline">
                    <div class="form-group">
                      <label style="display:block">
                        {{'Rank'|translate:'app'|escape}}
                      </label>
                      {{use class="app\models\Rank2"}}
                      {{$_ranks = Rank2::find()->orderBy('[[id]] DESC')->asArray()->all()}}
                      <select name="rank" id="battle-input2-form--ranked--rank" class="form-control">
                        {{foreach $_ranks as $_rank}}
                          <option value="{{$_rank.key|escape}}">
                            {{$_rank.name|translate:'app-rank'|escape}}
                          </option>
                        {{/foreach}}
                      </select>
                      &#32;<span class="fa fa-fw fa-arrow-right"></span>&#32;
                      <select name="rank_after" id="battle-input2-form--ranked--rank-after" class="form-control">
                        {{foreach $_ranks as $_rank}}
                          <option value="{{$_rank.key|escape}}">
                            {{$_rank.name|translate:'app-rank'|escape}}
                          </option>
                        {{/foreach}}
                      </select>
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input2-form--ranked--kill-or-assist">
                        {{'Kill or Assist'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--ranked--kill-or-assist" name="kill_or_assist" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input2-form--ranked--special">
                        {{'Specials'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--ranked--special" name="special" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                </div>

                <div class="form-group text-right">
                  <span class="visible-xs-inline" aria-hidden="true">
                    <span class="next-stages-will-arrive-in-2">
                      <span class="next-stages-will-arrive-in-2--value">-:--:--</span>
                    </span>
                    &#32;
                  </span>
                  <input type="hidden" id="battle-input2-form--ranked--uuid" name="uuid" value="">
                  <input type="hidden" id="battle-input2-form--ranked--end_at" name="end_at" value="">
                  <button type="button" class="btn btn-primary" id="battle-input2-form--ranked--submit" data-form="_{{$_prefix|escape}}_ranked" disabled>
                    <span class="far fa-save fa-fw"></span>{{'Save!'|translate:'app'|escape}}
                  </button>
                </div>
              </form>
            </div><!-- panel -->
            <div role="tabpanel" class="tab-pane" id="_{{$_prefix|escape}}_fest">
              <form class="battle-input-form" id="battle-input2-form--fest" action="#" onsubmit="return !1" data-apikey="{{$app->user->identity->api_key|escape}}">
                <input type="hidden" name="agent" value="{{$_agentName|escape}}">
                <input type="hidden" name="agent_version" value="{{$_agentVersion|escape}}" data-version="{{$_agentVersion|escape}}" data-revision="{{$_agentRevision|escape}}">

                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group">
                      <input type="hidden" id="battle-input2-form--fest--rule" name="rule" value="">
                      <input type="hidden" id="battle-input2-form--fest--mode" name="mode" value="fest">
                      <input type="text" id="battle-input2-form--fest--rule--label" value="" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group">
                      <select id="battle-input2-form--fest--lobby" name="lobby" class="form-control">
                        <option value="standard">
                          {{'Splatfest (Solo)'|translate:'app-rule2'|escape}}
                        </option>
                        <option value="squad_4">
                          {{'Splatfest (Team)'|translate:'app-rule2'|escape}}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!--h5>{{'Weapon'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <select class="form-control battle-input2-form--weapons" id="battle-input2-form--fest--weapon" name="weapon">
                  </select>
                </div>

                <!--h5>{{'Stages'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <select class="form-control" id="battle-input2-form--fest--stage" name="stage">
                    {{use class="app\models\Map2"}}
                    {{foreach Map2::getSortedMap() as $_key => $_name}}
                      <option value="{{$_key|escape}}">
                        {{$_name|escape}}
                      </option>
                    {{/foreach}}
                  </select>
                </div>

                <!--h5>{{'Result'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <input type="hidden" id="battle-input2-form--fest--result" name="result" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--fest--result" data-value="win">
                        {{'Win'|translate:'app'|escape}}
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--fest--result" data-value="lose">
                        {{'Lose'|translate:'app'|escape}}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-12 col-sm-6"></div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input2-form--fest--kill">
                        {{'Kill or Assist'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--fest--kill-or-assist" name="kill_or_assist" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input2-form--fest--death">
                        {{'Specials'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--fest--special" name="special" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <!--
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input2-form--fest--kill">
                        {{'Kills'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--fest--kill" name="kill" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input2-form--fest--death">
                        {{'Deaths'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--fest--death" name="death" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  -->
                </div>

                <div class="form-group text-right">
                  <span class="visible-xs-inline" aria-hidden="true">
                    <span class="next-stages-will-arrive-in-2">
                      <span class="next-stages-will-arrive-in-2--value">-:--:--</span>
                    </span>
                    &#32;
                  </span>
                  <input type="hidden" id="battle-input2-form--fest--uuid" name="uuid" value="">
                  <input type="hidden" id="battle-input2-form--fest--end_at" name="end_at" value="">
                  <button type="button" class="btn btn-primary" id="battle-input2-form--fest--submit" data-form="_{{$_prefix|escape}}_fest" disabled>
                    <span class="far fa-fw fa-save"></span>{{'Save!'|translate:'app'|escape}}
                  </button>
                </div>
              </form>
            </div><!-- panel -->
          </div>
        </div>
        <div class="modal-footer">
          <p class="text-left">
            {{$_linkAsset = \app\assets\AppLinkAsset::register($this)}}
            {{'Recommended to Android users:'|translate:'app'|escape}}&#32;
            {{if $app->language === 'ja-JP'}}
              {{$_linkAsset->ikaRecJa}}&#32;
              <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec2" target="_blank">
                イカレコ 2
              </a>
            {{else}}
              {{$_linkAsset->ikaRecEn}}&#32;
              <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec2" target="_blank">
                IkaRec 2
              </a>
            {{/if}}
          </p>
        </div>
      </div>
    </div>
  </div>
{{/strip}}
