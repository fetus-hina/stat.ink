<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\assets\AppLinkAsset;
use app\assets\BattleInputAsset;
use app\components\Version;
use app\models\Map2;
use app\models\Rank2;
use app\models\Rule2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

BattleInputAsset::register($this);

$_prefix = substr(hash('sha1', __FILE__), 0, 8);

$_agentName = sprintf('%s web client', Yii::$app->name);
$_agentVersion = Yii::$app->version === 'DEVELOPMENT'
  ? Yii::$app->version
  : sprintf('v%s', Yii::$app->version);
$_agentRevision = Version::getShortRevision();

$_maps = Map2::getSortedMap();
?>
<?= Html::beginTag('div', [
  'aria' => [
    'labelledby' => 'inputModalLabel',
    'modal' => 'true',
  ],
  'class' => ['battle-input-modal', 'fade', 'modal'],
  'data' => [
    'translate' => [
      'Favorite Weapons' => Yii::t('app', 'Favorite Weapons'),
    ],
  ],
  'id' => 'inputModal2',
  'role' => 'dialog',
  'tabindex' => '-1',
]) . "\n" ?>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <?= Html::tag(
          'button',
          '<span aria-hidden="true" class="fas fa-fw fa-times"></span>',
          [
            'type' => 'button',
            'class' => 'close',
            'data' => [
              'dismiss' => 'modal',
            ],
            'aria' => [
              'label' => Yii::t('app', 'Close'),
            ],
          ]
        ) . "\n" ?>
        <h4 class="modal-title" id="inputModalLabel">
          <?= Html::encode(Yii::t('app', 'Input new battle results') . ' (α)') . "\n" ?>
          <span class="hidden-xs" style="font-weight:normal" aria-hidden="true">
            <span class="next-stages-will-arrive-in-2">
              <span class="next-stages-will-arrive-in-2--value">-:--:--</span>
            </span>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" role="tablist" style="margin-bottom:15px">
          <li role="presentation" class="active">
            <?= Html::a(
              Html::encode(Yii::t('app-rule2', 'Regular')),
              "#_{$_prefix}_regular",
              ['data-toggle' => 'tab']
            ) . "\n" ?>
          </li>
          <li role="presentation" class="">
            <?= Html::a(
              Html::encode(Yii::t('app-rule2', 'Ranked') . ' / ' . Yii::t('app-rule2', 'League')),
              "#_{$_prefix}_ranked",
              ['data-toggle' => 'tab']
            ) . "\n" ?>
          </li>
          <li role="presentation" class="">
            <?= Html::a(
              Html::encode(Yii::t('app-rule2', 'Splatfest')),
              "#_{$_prefix}_fest",
              ['data-toggle' => 'tab']
            ) . "\n" ?>
          </li>
        </ul>
        <div class="tab-content">
          <?= Html::beginTag('div', [
            'role' => 'tabpanel',
            'class' => 'tab-pane active',
            'id' => "_{$_prefix}_regular",
          ]) . "\n" ?>
            <?= Html::beginTag('form', [
              'class' => 'battle-input-form',
              'id' => "battle-input2-form--regular",
              'action' => '#',
              'onsubmit' => 'return !1',
              'data-apikey' => Yii::$app->user->identity->api_key,
            ]) . "\n" ?>
              <?= Html::hiddenInput('agent', $_agentName) . "\n" ?>
              <?= Html::hiddenInput('agent_version', $_agentVersion, ['data-version' => $_agentVersion, 'data-revision' => $_agentRevision]) . "\n" ?>

              <div class="row">
                <div class="col-xs-12">
                  <div class="form-group">
                    <input type="hidden" id="battle-input2-form--regular--rule" name="rule" value="nawabari">
                    <input type="hidden" id="battle-input2-form--regular--mode" name="mode" value="regular">
                    <input type="hidden" id="battle-input2-form--regular--lobby" name="lobby" value="standard">
                    <?= Html::textInput(null, Yii::t('app-rule2', 'Turf War'), [
                      'class' => 'form-control',
                      'id' => 'battle-input2-form--regular--rule--label',
                      'readonly' => true,
                    ]) . "\n" ?>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <select class="form-control battle-input2-form--weapons" id="battle-input2-form--regular--weapon" name="weapon">
                </select>
              </div>

              <div class="form-group">
                <select class="form-control" id="battle-input2-form--regular--stage" name="stage">
<?php foreach ($_maps as $_k => $_name) { ?>
<?php if (substr($_k, 0, 7) !== 'mystery'){ ?>
                  <?= Html::tag('option', Html::encode($_name), ['value' => $_k]) . "\n" ?>
<?php } ?>
<?php } ?>
                </select>
              </div>

              <div class="form-group">
                <input type="hidden" id="battle-input2-form--regular--result" name="result" value="">
                <div class="row">
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--regular--result" data-value="win">
                      <?= Html::encode(Yii::t('app', 'Win')) . "\n" ?>
                    </button>
                  </div>
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--regular--result" data-value="lose">
                      <?= Html::encode(Yii::t('app', 'Lose')) . "\n" ?>
                    </button>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-12 col-sm-6">
                  <div class="form-group">
                    <label for="battle-input2-form--regular--point">
                      <?= Html::encode(Yii::t('app', 'Turf inked (including bonus)')) . "\n" ?>
                    </label>
                    <input type="number" id="battle-input2-form--regular--point" name="my_point" min="0" class="form-control" pattern="\d+" inputmode="numeric">
                  </div>
                </div>
                <div class="col-xs-6 col-sm-3">
                  <div class="form-group">
                    <label for="battle-input2-form--regular--kill-or-assist">
                      <?= Html::encode(Yii::t('app', 'Kill or Assist')) . "\n" ?>
                    </label>
                    <input type="number" id="battle-input2-form--regular--kill-or-assist" name="kill_or_assist" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                  </div>
                </div>
                <div class="col-xs-6 col-sm-3">
                  <div class="form-group">
                    <label for="battle-input2-form--regular--special">
                      <?= Html::encode(Yii::t('app', 'Specials')) . "\n" ?>
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
                </span>
                <input type="hidden" id="battle-input2-form--regular--uuid" name="uuid" value="">
                <input type="hidden" id="battle-input2-form--regular--end_at" name="end_at" value="">
                <?= Html::tag(
                  'button',
                  implode('', [
                    Html::tag('span', '', ['class' => 'far fa-fw fa-save']),
                    Html::encode(Yii::t('app', 'Save!')),
                  ]),
                  [
                    'type' => 'button',
                    'class' => 'btn btn-primary',
                    'id' => 'battle-input2-form--regular--submit',
                    'data-form' => "_{$_prefix}_regular",
                    'disabled' => true,
                  ]
                ) . "\n" ?>
              </div>
            </form>
          </div><!-- panel -->
          <?= Html::beginTag('div', [
            'role' => 'tabpanel',
            'class' => 'tab-pane',
            'id' => "_{$_prefix}_ranked",
          ]) . "\n" ?>
            <?= Html::beginTag('form', [
              'class' => 'battle-input-form',
              'id' => "battle-input2-form--ranked",
              'action' => '#',
              'onsubmit' => 'return !1',
              'data-apikey' => Yii::$app->user->identity->api_key,
            ]) . "\n" ?>
              <?= Html::hiddenInput('agent', $_agentName) . "\n" ?>
              <?= Html::hiddenInput('agent_version', $_agentVersion, ['data-version' => $_agentVersion, 'data-revision' => $_agentRevision]) . "\n" ?>

              <div class="row">
                <div class="col-xs-6">
                  <div class="form-group">
                    <select id="battle-input2-form--ranked--rule" name="rule" class="form-control">
<?php foreach (Rule2::getSortedAll('gachi') as $_k => $_n): ?>
                      <?= Html::tag('option', Html::encode($_n), ['value' => $_k]) . "\n" ?>
<?php endforeach ?>
                    </select>
                    <input type="hidden" id="battle-input2-form--ranked--mode" name="mode" value="gachi">
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="form-group">
                    <select id="battle-input2-form--ranked--lobby" name="lobby" class="form-control">
                      <option value="standard">
                        <?= Html::encode(Yii::t('app-rule2', 'Ranked Battle (Solo)')) . "\n" ?>
                      </option>
                      <option value="squad_2">
                        <?= Html::encode(Yii::t('app-rule2', 'League Battle (Twin)')) . "\n" ?>
                      </option>
                      <option value="squad_4">
                        <?= Html::encode(Yii::t('app-rule2', 'League Battle (Quad)')) . "\n" ?>
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <select class="form-control battle-input2-form--weapons" id="battle-input2-form--ranked--weapon" name="weapon">
                </select>
              </div>

              <div class="form-group">
                <select class="form-control" id="battle-input2-form--ranked--stage" name="stage">
<?php foreach ($_maps as $_k => $_name) { ?>
<?php if (substr($_k, 0, 7) !== 'mystery') { ?>
                  <?= Html::tag('option', Html::encode($_name), ['value' => $_k]) . "\n" ?>
<?php } ?>
<?php } ?>
                </select>
              </div>

              <div class="form-group">
                <input type="hidden" id="battle-input2-form--ranked--result" name="result" value="">
                <div class="row">
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--ranked--result" data-value="win">
                      <?= Html::encode(Yii::t('app', 'Win')) . "\n" ?>
                    </button>
                  </div>
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--ranked--result" data-value="lose">
                      <?= Html::encode(Yii::t('app', 'Lose')) . "\n" ?>
                    </button>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <input type="hidden" id="battle-input2-form--ranked--knock_out" name="knock_out" value="">
                <div class="row">
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-default btn-block battle-input2-form--knock_out" data-target="battle-input2-form--ranked--knock_out" data-value="yes">
                      <?= Html::encode(Yii::t('app', 'Knockout')) . "\n" ?>
                    </button>
                  </div>
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-default btn-block battle-input2-form--knock_out" data-target="battle-input2-form--ranked--knock_out" data-value="no">
                      <?= Html::encode(Yii::t('app', 'Time is up')) . "\n" ?>
                    </button>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-12 col-sm-6 form-inline">
                  <div class="form-group">
                    <label style="display:block">
                      <?= Html::encode(Yii::t('app', 'Rank')) . "\n" ?>
                    </label>
                    <select name="rank" id="battle-input2-form--ranked--rank" class="form-control">
<?php $ranks = Rank2::find()->orderBy(['id' => SORT_DESC])->asArray()->all() ?>
<?php foreach ($ranks as $rank): ?>
                      <?= Html::tag(
                        'option',
                        Html::encode(Yii::t('app-rank', $rank['name'])),
                        ['value' => $rank['key']]
                      ) . "\n" ?>
<?php endforeach ?>
                    </select>
                    <span class="fas fa-fw fa-arrow-right"></span>
                    <select name="rank_after" id="battle-input2-form--ranked--rank-after" class="form-control">
<?php foreach ($ranks as $rank): ?>
                      <?= Html::tag(
                        'option',
                        Html::encode(Yii::t('app-rank', $rank['name'])),
                        ['value' => $rank['key']]
                      ) . "\n" ?>
<?php endforeach ?>
                    </select>
                  </div>
                </div>
                <div class="col-xs-6 col-sm-3">
                  <div class="form-group">
                    <label for="battle-input2-form--ranked--kill-or-assist">
                      <?= Html::encode(Yii::t('app', 'Kill or Assist')) . "\n" ?>
                    </label>
                    <input type="number" id="battle-input2-form--ranked--kill-or-assist" name="kill_or_assist" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                  </div>
                </div>
                <div class="col-xs-6 col-sm-3">
                  <div class="form-group">
                    <label for="battle-input2-form--ranked--special">
                      <?= Html::encode(Yii::t('app', 'Specials')) . "\n" ?>
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
                </span>
                <input type="hidden" id="battle-input2-form--ranked--uuid" name="uuid" value="">
                <input type="hidden" id="battle-input2-form--ranked--end_at" name="end_at" value="">
                <?= Html::tag(
                  'button',
                  implode('', [
                    Html::tag('span', '', ['class' => 'far fa-fw fa-save']),
                    Html::encode(Yii::t('app', 'Save!')),
                  ]),
                  [
                    'type' => 'button',
                    'class' => 'btn btn-primary',
                    'id' => 'battle-input2-form--ranked--submit',
                    'data-form' => "_{$_prefix}_ranked",
                    'disabled' => true,
                  ]
                ) . "\n" ?>
              </div>
            </form>
          </div><!-- panel -->
          <?= Html::beginTag('div', [
            'role' => 'tabpanel',
            'class' => 'tab-pane',
            'id' => "_{$_prefix}_fest",
          ]) . "\n" ?>
            <?= Html::beginTag('form', [
              'class' => 'battle-input-form',
              'id' => "battle-input2-form--fest",
              'action' => '#',
              'onsubmit' => 'return !1',
              'data-apikey' => Yii::$app->user->identity->api_key,
            ]) . "\n" ?>
              <?= Html::hiddenInput('agent', $_agentName) . "\n" ?>
              <?= Html::hiddenInput('agent_version', $_agentVersion, ['data-version' => $_agentVersion, 'data-revision' => $_agentRevision]) . "\n" ?>

              <div class="row">
                <div class="col-xs-6">
                  <div class="form-group">
                    <input type="hidden" id="battle-input2-form--fest--rule" name="rule" value="nawabari">
                    <input type="hidden" id="battle-input2-form--fest--mode" name="mode" value="fest">
                    <?= Html::textInput('', Yii::t('app-rule2', 'Turf War'), [
                      'id' => 'battle-input2-form--fest--rule--label',
                      'class' => 'form-control',
                      'readonly' => true,
                    ]) . "\n" ?>
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="form-group">
                    <select id="battle-input2-form--fest--lobby" name="lobby" class="form-control">
                      <option value="fest_normal">
                        <?= Html::encode(Yii::t('app-rule2', 'Splatfest (Normal)')) . "\n" ?>
                      </option>
                      <option value="fest_pro">
                        <?= Html::encode(Yii::t('app-rule2', 'Splatfest (Pro)')) . "\n" ?>
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <select class="form-control battle-input2-form--weapons" id="battle-input2-form--fest--weapon" name="weapon">
                </select>
              </div>

              <div class="form-group">
                <select class="form-control" id="battle-input2-form--fest--stage" name="stage">
<?php foreach ($_maps as $_k => $_name) { ?>
                  <?= Html::tag('option', Html::encode($_name), ['value' => $_k]) . "\n" ?>
<?php } ?>
                </select>
              </div>

              <div class="form-group">
                <input type="hidden" id="battle-input2-form--fest--result" name="result" value="">
                <div class="row">
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--fest--result" data-value="win">
                      <?= Html::encode(Yii::t('app', 'Win')) . "\n" ?>
                    </button>
                  </div>
                  <div class="col-xs-6">
                    <button type="button" class="btn btn-default btn-block battle-input2-form--result" data-target="battle-input2-form--fest--result" data-value="lose">
                      <?= Html::encode(Yii::t('app', 'Lose')) . "\n" ?>
                    </button>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-12 col-sm-6"></div>
                <div class="col-xs-6 col-sm-3">
                  <div class="form-group">
                    <label for="battle-input2-form--fest--kill">
                      <?= Html::encode(Yii::t('app', 'Kill or Assist')) . "\n" ?>
                    </label>
                    <input type="number" id="battle-input2-form--fest--kill-or-assist" name="kill_or_assist" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                  </div>
                </div>
                <div class="col-xs-6 col-sm-3">
                  <div class="form-group">
                    <label for="battle-input2-form--fest--death">
                      <?= Html::encode(Yii::t('app', 'Specials')) . "\n" ?>
                    </label>
                    <input type="number" id="battle-input2-form--fest--special" name="special" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                  </div>
                </div>
              </div>

              <div class="form-group text-right">
                <span class="visible-xs-inline" aria-hidden="true">
                  <span class="next-stages-will-arrive-in-2">
                    <span class="next-stages-will-arrive-in-2--value">-:--:--</span>
                  </span>
                </span>
                <input type="hidden" id="battle-input2-form--fest--uuid" name="uuid" value="">
                <input type="hidden" id="battle-input2-form--fest--end_at" name="end_at" value="">
                <?= Html::tag(
                  'button',
                  implode('', [
                    Html::tag('span', '', ['class' => 'far fa-fw fa-save']),
                    Html::encode(Yii::t('app', 'Save!')),
                  ]),
                  [
                    'type' => 'button',
                    'class' => 'btn btn-primary',
                    'id' => 'battle-input2-form--fest--submit',
                    'data-form' => "_{$_prefix}_fest",
                    'disabled' => true,
                  ]
                ) . "\n" ?>
              </div>
            </form>
          </div><!-- panel -->
        </div>
      </div>
    </div>
  </div>
</div>
