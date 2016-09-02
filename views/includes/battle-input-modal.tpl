{{strip}}
  {{use class="rmrevin\yii\fontawesome\FontAwesome" as="FA"}}
  {{\rmrevin\yii\fontawesome\AssetBundle::register($this)|@void}}
  {{\app\assets\BattleInputAsset::register($this)|@void}}

  {{$_prefix = 'input-modal-internal'|sha1|substr:0:8}}

  {{$_agentName = $app->name|cat:' web client'}}
  {{$_agentVersion = 'v'|cat:$app->version}}
  {{$_agentRevision = \app\components\Version::getShortRevision()}}

  <div class="modal fade" id="inputModal" tabindex="-1" role="dialog" aria-labelledby="inputModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="{{'Close'|translate:'app'|escape}}">
            <span aria-hidden="true">{{FA::icon('times')->tag('span')}}</span>
          </button>
          <h4 class="modal-title" id="inputModalLabel">
            {{'Input new battle results'|translate:'app'|escape}}&#32;
            (β)
            <span class="hidden-xs" style="font-weight:normal" aria-hidden="true">
              &#32;
              <span class="next-stages-will-arrive-in">
                <span class="next-stages-will-arrive-in--value">-:--:--</span>
              </span>
            </span>
          </h4>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs" role="tablist" style="margin-bottom:15px">
            <li role="presentation" class="active">
              <a href="#_{{$_prefix|escape}}_regular" data-toggle="tab">
                <span class="hidden-xs">{{'Regular Battle'|translate:'app-rule'|escape}}</span>
                <span class="visible-xs-inline">{{'Regular'|translate:'app-rule'|escape}}</span>
              </a>
            </li>
            <li role="presentation">
              <a href="#_{{$_prefix|escape}}_gachi" data-toggle="tab">
                <span class="hidden-xs">{{'Ranked Battle'|translate:'app-rule'|escape}}</span>
                <span class="visible-xs-inline">{{'Ranked'|translate:'app-rule'|escape}}</span>
              </a>
            </li>
            <li role="presentation">
              <a href="#_{{$_prefix|escape}}_private" data-toggle="tab">
                <span class="hidden-xs">{{'Private Battle'|translate:'app-rule'|escape}}</span>
                <span class="visible-xs-inline">{{'Private'|translate:'app-rule'|escape}}</span>
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="_{{$_prefix|escape}}_regular">
              <form class="battle-input-form" id="battle-input-form--regular" action="#" onsubmit="return !1">
                <input type="hidden" name="apikey" value="{{$app->user->identity->api_key|escape}}">
                <input type="hidden" name="agent" value="{{$_agentName|escape}}">
                <input type="hidden" name="agent_version" value="{{$_agentVersion|escape}}" data-version="{{$_agentVersion|escape}}" data-revision="{{$_agentRevision|escape}}">

                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group">
                      <input type="hidden" id="battle-input-form--regular--rule" name="rule" value="">
                      <input type="text" id="battle-input-form--regular--rule--label" value="" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group">
                      <select id="battle-input-form--regular--lobby" name="lobby" class="form-control" readonly>
                        <option value="standard">
                          {{use class="app\models\Lobby"}}
                          {{$_lobby = Lobby::findOne(['key' => 'standard'])}}
                          {{$_lobby->name|translate:'app-rule'|escape}}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!--h5>{{'Weapon'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <select class="form-control battle-input-form--weapons" id="battle-input-form--regular--weapon" name="weapon">
                  </select>
                </div>

                <!--h5>{{'Stages'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <input type="hidden" id="battle-input-form--regular--stage" name="map" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--stages" data-game-mode="regular" data-target="battle-input-form--regular--stage">
                        Stage A
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--stages" data-game-mode="regular" data-target="battle-input-form--regular--stage">
                        Stage B
                      </button>
                    </div>
                  </div>
                </div>

                <!--h5>{{'Result'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <input type="hidden" id="battle-input-form--regular--result" name="result" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--result" data-target="battle-input-form--regular--result" data-value="win">
                        {{'Win'|translate:'app'|escape}}
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--result" data-target="battle-input-form--regular--result" data-value="lose">
                        {{'Lose'|translate:'app'|escape}}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                      <label for="battle-input-form--regular--point">
                        {{'Turf inked (including bonus)'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input-form--regular--point" name="my_point" min="0" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input-form--regular--kill">
                        {{'Kills'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input-form--regular--kill" name="kill" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input-form--regular--death">
                        {{'Deaths'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input-form--regular--death" name="death" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                </div>

                <div class="form-group text-right">
                  <span class="visible-xs-inline" aria-hidden="true">
                    <span class="next-stages-will-arrive-in">
                      <span class="next-stages-will-arrive-in--value">-:--:--</span>
                    </span>
                    &#32;
                  </span>
                  <input type="hidden" id="battle-input-form--regular--uuid" name="uuid" value="">
                  <button type="button" class="btn btn-primary" id="battle-input-form--regular--submit" data-form="_{{$_prefix|escape}}_regular" disabled>
                    {{FA::icon('floppy-o')->tag('span')}} {{'Save!'|translate:'app'|escape}}
                  </button>
                </div>
              </form>
            </div>
            <div role="tabpanel" class="tab-pane" id="_{{$_prefix|escape}}_gachi">
              <form class="battle-input-form" id="battle-input-form--gachi" action="#" onsubmit="return !1">
                <input type="hidden" name="apikey" value="{{$app->user->identity->api_key|escape}}">
                <input type="hidden" name="agent" value="{{$_agentName|escape}}">
                <input type="hidden" name="agent_version" value="{{$_agentVersion|escape}}" data-version="{{$_agentVersion|escape}}" data-revision="{{$_agentRevision|escape}}">

                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group">
                      <input type="hidden" id="battle-input-form--gachi--rule" name="rule" value="">
                      <input type="text" id="battle-input-form--gachi--rule--label" value="" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <select name="lobby" class="form-control">
                      {{use class="app\models\Lobby"}}
                      {{$_q = Lobby::find()
                          ->orderBy('id')
                          ->andWhere(['or',
                              ['key' => 'standard'],
                              ['like', 'key', 'squad_%', false]
                            ])
                          ->asArray()
                        }}
                      {{foreach $_q->all() as $_lobby}}
                        <option value="{{$_lobby.key|escape}}">
                          {{$_lobby.name|translate:'app-rule'|escape}}
                        </option>
                      {{/foreach}}
                    </select>
                  </div>
                </div>

                <!--h5>{{'Weapon'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <select class="form-control battle-input-form--weapons" id="battle-input-form--gachi--weapon" name="weapon">
                  </select>
                </div>

                <!--h5>{{'Stages'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <input type="hidden" id="battle-input-form--gachi--stage" name="map" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--stages" data-game-mode="gachi" data-target="battle-input-form--gachi--stage">
                        Stage A
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--stages" data-game-mode="gachi" data-target="battle-input-form--gachi--stage">
                        Stage B
                      </button>
                    </div>
                  </div>
                </div>

                <!--h5>{{'Result'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <input type="hidden" id="battle-input-form--gachi--result" name="result" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--result" data-target="battle-input-form--gachi--result" data-value="win">
                        {{'Win'|translate:'app'|escape}}
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--result" data-target="battle-input-form--gachi--result" data-value="lose">
                        {{'Lose'|translate:'app'|escape}}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-12 col-sm-6 form-inline">
                    <div class="form-group">
                      <label style="display:block">
                        {{'Rank (after the battle)'|translate:'app'|escape}}
                      </label>
                      <select name="rank_after" id="battle-input-form--gachi--rank-after" class="form-control">
                        {{use class="app\models\Rank"}}
                        {{foreach Rank::find()->orderBy('[[id]] DESC')->asArray()->all() as $_rank}}
                          <option value="{{$_rank.key|escape}}">
                            {{$_rank.name|translate:'app-rank'|escape}}
                          </option>
                        {{/foreach}}
                      </select>
                      <input type="number" id="battle-input-form--gachi--rank-exp-after" name="rank_exp_after" class="form-control" min="0" max="99" pattern="\d+" inputmode="numeric" placeholder="0～99">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input-form--gachi--kill">
                        {{'Kills'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input-form--gachi--kill" name="kill" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input-form--gachi--death">
                        {{'Deaths'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input-form--gachi--death" name="death" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                </div>

                <div class="form-group text-right">
                  <span class="visible-xs-inline" aria-hidden="true">
                    <span class="next-stages-will-arrive-in">
                      <span class="next-stages-will-arrive-in--value">-:--:--</span>
                    </span>
                    &#32;
                  </span>
                  <input type="hidden" id="battle-input-form--gachi--uuid" name="uuid" value="">
                  <button type="button" class="btn btn-primary" id="battle-input-form--gachi--submit" data-form="_{{$_prefix|escape}}_gachi" disabled>
                    {{FA::icon('floppy-o')->tag('span')}} {{'Save!'|translate:'app'|escape}}
                  </button>
                </div>
              </form>
            </div>
            <div role="tabpanel" class="tab-pane" id="_{{$_prefix|escape}}_private">
              <p>
                Not implemented yet.
              </p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <p class="text-left">
            {{$_linkAsset = \app\assets\AppLinkAsset::register($this)}}
            {{'Recommended to Android users:'|translate:'app'|escape}}&#32;
            {{if $app->language === 'ja-JP'}}
              {{$_linkAsset->ikaRecJa}}&#32;
              <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec" target="_blank">
                イカレコ
              </a>
            {{else}}
              {{$_linkAsset->ikaRecEn}}&#32;
              <a href="https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec" target="_blank">
                IkaRec (English version)
              </a>
            {{/if}}
          </p>
        </div>
      </div>
    </div>
  </div>
{{/strip}}
