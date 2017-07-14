{{strip}}
  {{use class="rmrevin\yii\fontawesome\FontAwesome" as="FA"}}
  {{\rmrevin\yii\fontawesome\AssetBundle::register($this)|@void}}
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
            <span aria-hidden="true">{{FA::icon('times')->tag('span')}}</span>
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
              <a href="#_{{$_prefix|escape}}_fest" data-toggle="tab">
                {{'Splatfest'|translate:'app-rule'|escape}}
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="_{{$_prefix|escape}}_fest">
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
                  <input type="hidden" id="battle-input2-form--fest--stage" name="stage" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--stages" data-game-mode="fest" data-target="battle-input2-form--fest--stage">
                        Stage A
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input2-form--stages" data-game-mode="fest" data-target="battle-input2-form--fest--stage">
                        Stage B
                      </button>
                    </div>
                  </div>
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
                  <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                      <label for="battle-input2-form--fest--point">
                        {{'Turf inked (including bonus)'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input2-form--fest--point" name="my_point" min="0" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
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
                    {{FA::icon('floppy-o')->tag('span')}} {{'Save!'|translate:'app'|escape}}
                  </button>
                </div>
              </form>
            </div>
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
