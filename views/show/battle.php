<?php

declare(strict_types=1);

use app\assets\AppAsset;
use app\assets\BattleEditAsset;
use app\assets\BattlePrivateNoteAsset;
use app\assets\BattleTimelineAsset;
use app\assets\GearCalcAsset;
use app\assets\GraphIconAsset;
use app\assets\PhotoSwipeSimplifyAsset;
use app\components\helpers\ArrayHelper;
use app\components\helpers\Html;
use app\components\helpers\IkalogVersion;
use app\components\widgets\AdWidget;
use app\components\widgets\EmbedVideo;
use app\components\widgets\FA;
use app\components\widgets\KillRatioBadgeWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\TimestampColumnWidget;
use app\models\Battle;
use app\models\BattleDeathReason;
use app\models\Special;
use app\models\User;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var Battle $battle
 * @var User $user
 * @var View $this
 */

$this->context->layout = 'main';

$user = $battle->user;
$canonicalUrl = Url::to(
  ['show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
  true
);
$title = Yii::t('app', 'Results of {name}\'s Battle', ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$summary = [];
if ($battle->rule) {
  $summary[] = Yii::t('app-rule', $battle->rule->name);
}
if ($battle->map) {
  $summary[] = Yii::t('app-map', $battle->map->name);
}
if ($battle->is_win !== null) {
  $summary[] = $battle->is_win ? Yii::t('app', 'Won') : Yii::t('app', 'Lost');
}
$this->registerLinkTag(['rel' => 'canonical', 'href' => $canonicalUrl]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'photo']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $canonicalUrl]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => implode(' | ', $summary)]);
if ($user->twitter != '') {
  $this->registerMetaTag([
    'name' => 'twitter:creator',
    'content' => sprintf('@%s', $user->twitter),
  ]);
}
if ($battle->previousBattle) {
  $this->registerLinkTag([
    'rel' => 'prev',
    'href' => Url::to(
      ['show/battle',
        'screen_name' => $user->screen_name,
        'battle' => $battle->previousBattle->id,
      ],
      true
    ),
  ]);
}
if ($battle->nextBattle) {
  $this->registerLinkTag([
    'rel' => 'next',
    'href' => Url::to(
      ['show/battle',
        'screen_name' => $user->screen_name,
        'battle' => $battle->nextBattle->id,
      ],
      true
    ),
  ]);
}

$this->registerJsVar('gearAbilities', (array)$battle->gearAbilities);

GearCalcAsset::register($this);
$this->registerCss(implode('', [
  '#battle th{width:15em}',
  '@media(max-width:30em){#battle th{width:auto}}',
  '.image-container{margin-bottom:15px}',
]));

$specials = Special::find()->asArray()->all();
?>
<div itemscope itemtype="http://schema.org/BlogPosting" class="container">
  <span itemscope itemtype="http://schema.org/BreadcrumbList">
    <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
      <?= Html::tag('meta', '', ['itemprop' => 'url', 'content' => Url::home(true)]) . "\n" ?>
      <?= Html::tag('meta', '', ['itemprop' => 'title', 'content' => Yii::$app->name]) . "\n" ?>
    </span>
    <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
      <?= Html::tag('meta', '', [
        'itemprop' => 'url',
        'content' => Url::to(
          ['show/user',
            'screen_name' => $user->screen_name,
          ],
          true
        ),
      ]) . "\n" ?>
      <?= Html::tag('meta', '', ['itemprop' => 'title', 'content' => $user->name]) . "\n" ?>
    </span>
  </span>
  <h1 itemprop="headline"><?=
    Yii::t('app', 'Results of {name}\'s Battle', [
      'name' => Html::a(
        Html::encode($user->name),
        ['show/user', 'screen_name' => $user->screen_name]
      ),
    ])
  ?></h1>

<?php if ($battle->agent && IkalogVersion::isOutdated($battle)) { ?>
  <?= Html::tag(
    'p',
    Html::encode(
      Yii::t(
        'app',
        'This battle was recorded with an outdated version of IkaLog. Please upgrade to the latest version.'
      )
    ),
    ['style' => [
      'font-weight' => 'bold',
      'color' => '#f00',
    ]]
  ) . "\n" ?>
<?php } ?>

  <?= SnsWidget::widget() . "\n" ?>

<?php if ($battle->battleImageJudge || $battle->battleImageResult) { ?>
<?php PhotoSwipeSimplifyAsset::register($this) ?>
    <div class="row mb-3" data-pswp="">
<?php if ($battle->battleImageJudge) { ?>
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 image-container">
        <?= Html::a(
          implode('', [
            Html::img($battle->battleImageJudge->url, [
              'itemprop' => 'url',
              'class' => 'w-100 h-auto',
            ]),
            Html::tag('meta', '', ['itemprop' => 'width', 'content' => 640]),
            Html::tag('meta', '', ['itemprop' => 'height', 'content' => 360]),
          ]),
          $battle->battleImageJudge->url,
          [
            'itemscope' => null,
            'itemprop' => 'image',
            'itemtype' => 'http://schema.org/ImageObject',
          ]
        ) . "\n" ?>
      </div>
<?php } ?>
<?php if ($battle->battleImageResult) { ?>
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 image-container">
        <?= Html::a(
          implode('', [
            Html::img($battle->battleImageResult->url, [
              'itemprop' => 'url',
              'class' => 'w-100 h-auto',
            ]),
            Html::tag('meta', '', ['itemprop' => 'width', 'content' => 640]),
            Html::tag('meta', '', ['itemprop' => 'height', 'content' => 360]),
          ]),
          $battle->battleImageResult->url,
          [
            'itemscope' => null,
            'itemprop' => 'image',
            'itemtype' => 'http://schema.org/ImageObject',
          ]
        ) . "\n" ?>
      </div>
      <?= Html::tag('meta', '', [
        'name' => 'twitter:image',
        'content' => Url::to($battle->battleImageResult->url, true),
      ]) . "\n" ?>
<?php } ?>
    </div>
<?php } ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
<?php if ($battle->previousBattle || $battle->nextBattle) { ?>
      <div class="row mb-3">
<?php if ($battle->previousBattle) { ?>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <?= Html::a(
            implode('', [
              (string)FA::fas('angle-double-left')->fw(),
              Html::encode(Yii::t('app', 'Prev. Battle')),
            ]),
            ['show/battle',
              'screen_name' => $user->screen_name,
              'battle' => $battle->previousBattle->id,
            ],
            ['class' => 'btn btn-default']
          ) . "\n" ?>
        </div>
<?php } ?>
<?php if ($battle->nextBattle) { ?>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pull-right text-right">
          <?= Html::a(
            implode('', [
              Html::encode(Yii::t('app', 'Next Battle')),
              (string)FA::fas('angle-double-right')->fw(),
            ]),
            ['show/battle',
              'screen_name' => $user->screen_name,
              'battle' => $battle->nextBattle->id,
            ],
            ['class' => 'btn btn-default']
          ) . "\n" ?>
        </div>
<?php } ?>
      </div>
<?php } ?>

<?php if ($battle->link_url && EmbedVideo::isSupported($battle->link_url)) { ?>
      <div class="mb-3">
        <?= EmbedVideo::widget([
          'url' => $battle->link_url,
        ]) . "\n" ?>
      </div>
<?php } ?>

      <table class="table table-striped" id="battle">
        <tbody>
<?php if ($battle->lobby) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Lobby')) ?></th>
            <td><?= Html::encode(Yii::t('app-rule', $battle->lobby->name)) ?></th>
          </tr>
<?php } ?>
<?php if ($battle->rule) { ?>
          <tr>
            <th><?= implode(' ', [
              Html::encode(Yii::t('app', 'Mode')),
              Html::a(
                (string)FA::fas('chart-pie')->fw(),
                ['show/user-stat-by-rule', 'screen_name' => $user->screen_name]
              ),
            ]) ?></th>
            <td><?= implode(' ', [
              Html::a(
                (string)FA::fas('search')->fw(),
                ['show/user',
                  'screen_name' => $user->screen_name,
                  'filter' => [
                    'rule' => $battle->rule->key,
                  ],
                ]
              ),
              Html::encode(Yii::t('app-rule', $battle->rule->name)),
            ]) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->map) { ?>
          <tr>
            <th><?= implode(' ', [
              Html::encode(Yii::t('app', 'Stage')),
              Html::a(
                (string)FA::fas('chart-pie')->fw(),
                ['show/user-stat-by-map', 'screen_name' => $user->screen_name],
              ),
            ]) ?></th>
            <td><?= implode(' ', [
              Html::a(
                (string)FA::fas('search')->fw(),
                ['show/user',
                  'screen_name' => $user->screen_name,
                  'filter' => [
                    'map' => $battle->map->key,
                  ],
                ]
              ),
              Html::tag(
                'span',
                Html::encode(Yii::t('app-map', $battle->map->name)),
                ['itemprop' => 'contentLocation']
              ),
            ]) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->weapon) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
            <td><?= implode(' ', [
              Html::a(
                (string)FA::fas('search')->fw(),
                ['show/user',
                  'screen_name' => $user->screen_name,
                  'filter' => [
                    'weapon' => $battle->weapon->key,
                  ],
                ]
              ),
              Html::encode(Yii::t('app-weapon', $battle->weapon->name)),
              Html::encode(sprintf('(%s)', implode(' / ', [
                Yii::t('app-subweapon', $battle->weapon->subweapon->name),
                Yii::t('app-special', $battle->weapon->special->name),
              ]))),
            ]) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->rank || $battle->rankAfter) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Rank')) ?></th>
            <td><?= implode(' ', array_filter(ArrayHelper::toFlatten([
              ($battle->rank)
                ? [
                  Html::encode(Yii::t('app-rank', $battle->rank->name)),
                  ($battle->rank_exp !== null)
                    ? Html::encode((string)$battle->rank_exp)
                    : ''
                ]
                : '?',
              (string)FA::fas('arrow-right')->fw(),
              ($battle->rankAfter)
                ? [
                  Html::encode(Yii::t('app-rank', $battle->rankAfter->name)),
                  ($battle->rank_exp_after !== null)
                    ? Html::encode((string)$battle->rank_exp_after)
                    : ''
                ]
                : '?',
            ]))) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->level || $battle->level_after) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Level')) ?></th>
            <td><?= implode(' ', [
              Html::encode($battle->level ?: '?'),
              (string)FA::fas('arrow-right')->fw(),
              Html::encode($battle->level_after ?: '?'),
            ]) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->festTitle || $battle->festTitleAfter) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Splatfest Title')) ?></th>
            <td>
<?php if ($battle->my_team_color_rgb) { ?>
              <?= Html::tag(
                'span',
                (string)FA::fas('square')->fw(),
                ['style' => [
                  'color' => '#' . $battle->my_team_color_rgb,
                ]]
              ) . "\n" ?>
<?php } ?>
              <?= implode(' ', array_filter(ArrayHelper::toFlatten([
                ($battle->festTitle)
                  ? [
                    Html::encode(Yii::t('app-fest', $battle->festTitle->getName($battle->gender), [
                      '***',
                      '***',
                    ])),
                    ($battle->fest_exp !== null)
                      ? Html::encode((string)$battle->fest_exp)
                      : '',
                  ]
                  : '?',
                (string)FA::fas('arrow-right')->fw(),
                ($battle->festTitleAfter)
                  ? [
                    Html::encode(
                      Yii::t('app-fest', $battle->festTitleAfter->getName($battle->gender), [
                        '***',
                        '***',
                      ])
                    ),
                    ($battle->fest_exp_after !== null)
                      ? Html::encode((string)$battle->fest_exp_after)
                      : '',
                  ]
                  : '?',
              ]))) . "\n" ?>
            </td>
          </tr>
<?php } ?>
<?php if ($battle->fest_power) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Splatfest Power')) ?></th>
            <td><?= Html::encode((string)$battle->fest_power) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->my_team_power || $battle->his_team_power) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'My Team Splatfest Power')) ?></th>
            <td><?= Html::encode($battle->my_team_power ?: '?') ?></td>
          </tr>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Their Team Splatfest Power')) ?></th>
            <td><?= Html::encode($battle->his_team_power ?: '?') ?></td>
          </tr>
<?php } ?>
<?php if ($battle->is_win !== null) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Result')) ?></th>
            <td>
<?php if ($battle->isGachi && $battle->is_knock_out !== null) { ?>
              <?= (
                ($battle->is_knock_out)
                  ? Html::tag('span', Html::encode(Yii::t('app', 'Knockout')), [
                    'class' => 'label label-info',
                  ])
                  : Html::tag('span', Html::encode(Yii::t('app', 'Time is up')), [
                    'class' => 'label label-warning',
                  ])
              ) . "\n" ?>
<?php } ?>
<?php if ($battle->is_win === null) { ?>
              ?
<?php } else { ?>
              <?= (
                ($battle->is_win)
                  ? Html::tag('span', Html::encode(Yii::t('app', 'Won')), [
                    'class' => 'label label-success',
                  ])
                  : Html::tag('span', Html::encode(Yii::t('app', 'Lost')), [
                    'class' => 'label label-danger',
                  ])
              ) . "\n" ?>
<?php } ?>
            </td>
          </tr>
<?php } ?>
<?php if ($battle->rank_in_team) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Rank in Team')) ?></th>
            <td><?= Html::encode((string)$battle->rank_in_team) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->kill !== null || $battle->death !== null) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Kills / Deaths')) ?></th>
            <td>
              <?= Html::encode(vsprintf('%s / %s', [
                ($battle->kill === null)
                  ? '?'
                  : Yii::$app->formatter->asInteger((int)$battle->kill),
                ($battle->death === null)
                  ? '?'
                  : Yii::$app->formatter->asInteger((int)$battle->death),
              ])) . "\n" ?>
<?php if ($battle->kill !== null && $battle->death !== null) { ?>
              <?= KillRatioBadgeWidget::widget([
                'kill' => $battle->kill,
                'death' => $battle->death,
              ]) . "\n" ?>
<?php } ?>
            </td>
          </tr>
<?php } ?>
<?php if ($battle->kill !== null && $battle->death !== null) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Kill Ratio')) ?></th>
            <td><?= ($battle->kill_ratio === null)
              ? Html::encode(Yii::t('app', 'N/A'))
              : Html::encode(Yii::$app->formatter->asDecimal((float)$battle->kill_ratio, 2))
            ?></td>
          </tr>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Kill Rate')) ?></th>
            <td><?= ($battle->kill_rate === null)
              ? Html::encode(Yii::t('app', 'N/A'))
              : Html::encode(Yii::$app->formatter->asPercent((float)$battle->kill_rate, 1))
            ?></td>
          </tr>
<?php } ?>
<?php if ($battle->max_kill_combo !== null) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Max Kill Combo')) ?></th>
            <td><?= Html::encode((string)$battle->max_kill_combo) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->max_kill_streak !== null) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Max Kill Streak')) ?></th>
            <td><?= Html::encode((string)$battle->max_kill_streak) ?></td>
          </tr>
<?php } ?>
<?php $deathReasons = BattleDeathReason::find()
  ->with(['reason'])
  ->andWhere(['battle_id' => $battle->id])
  ->orderBy(['{{battle_death_reason}}.[[count]]' => SORT_DESC])
  ->all() ?>
<?php if ($deathReasons) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Cause of Death')) ?></th>
            <td>
              <table>
                <tbody>
<?php foreach ($deathReasons as $deathReason) { ?>
                  <tr>
                    <td><?= Html::encode($deathReason->reason->getTranslatedName()) ?></td>
                    <td style="padding:0 10px">:</td>
                    <td><?= Html::encode(
                      Yii::t('app', '{nFormatted} {n, plural, =1{time} other{times}}', [
                        'n' => $deathReason->count,
                        'nFormatted' => Yii::$app->formatter->asDecimal($deathReason->count),
                      ])
                    ) ?></td>
                  </tr>
<?php } ?>
                </tbody>
              </table>
            </td>
          </tr>
<?php } ?>
<?php if ($battle->my_point) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Turf Inked + Bonus')) ?></th>
            <td><?= Html::encode(vsprintf('%s P', [
              ($battle->inked === null)
                ? Yii::$app->formatter->asInteger((int)$battle->my_point)
                : (
                  ($battle->is_win && $battle->bonus)
                    ? vsprintf('%s + %s', [
                      Yii::$app->formatter->asInteger((int)$battle->inked),
                      Yii::$app->formatter->asInteger((int)$battle->bonus->bonus),
                    ])
                    : Yii::$app->formatter->asInteger((int)$battle->inked)
                )
            ])) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->my_team_final_point || $battle->his_team_final_point) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'My Team Score')) ?></th>
            <td><?= Html::encode(vsprintf('%s P (%s)', [
              $battle->my_team_final_point
                ? Yii::$app->formatter->asInteger($battle->my_team_final_point)
                : '?',
              $battle->my_team_final_percent === null
                ? '? %'
                : Yii::$app->formatter->asPercent((float)$battle->my_team_final_percent / 100, 1),
            ])) ?></td>
          </tr>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Their Team Score')) ?></th>
            <td><?= Html::encode(vsprintf('%s P (%s)', [
              $battle->his_team_final_point
                ? Yii::$app->formatter->asInteger($battle->his_team_final_point)
                : '?',
              $battle->his_team_final_percent === null
                ? '? %'
                : Yii::$app->formatter->asPercent((float)$battle->his_team_final_percent / 100, 1),
            ])) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->my_team_count || $battle->his_team_count) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'My Team Count')) ?></th>
            <td><?= Html::encode($battle->my_team_count ?? '?') ?></td>
          </tr>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Their Team Count')) ?></th>
            <td><?= Html::encode($battle->his_team_count ?? '?') ?></td>
          </tr>
<?php } ?>
<?php if ($battle->cash || $battle->cash_after) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Cash')) ?></th>
            <td>
              <?= implode(' ', [
                ($battle->cash === null)
                  ? Html::encode('?')
                  : Html::encode(Yii::$app->formatter->asInteger((int)$battle->cash)),
                (string)FA::fas('arrow-right')->fw(),
                ($battle->cash_after === null)
                  ? Html::encode('?')
                  : Html::encode(Yii::$app->formatter->asInteger((int)$battle->cash_after)),
              ]) . "\n" ?>
            </td>
          </tr>
<?php } ?>
<?php if ($battle->headgear || $battle->clothing || $battle->shoes) { ?>
          <tr>
            <th>
              <?= Html::encode(Yii::t('app', 'Gear')) . "\n" ?>
<?php if ($battle->battleImageGear) { ?>
              <span data-pswp><?= Html::a(
                implode('', [
                  (string)FA::fas('image')->fw(),
                  Html::img($battle->battleImageGear->url, [
                    'width' => 1,
                    'height' => 1,
                    'style' => [
                      'opacity' => 0,
                    ],
                  ]),
                ]),
                Url::to($battle->battleImageGear->url, true)
              ) ?></span>
<?php } ?>
            </th>
            <td>
              <?= $this->render('_battle_gear', ['battle' => $battle]) . "\n" ?>
            </td>
          </tr>
<?php } ?>
<?php $_editable = (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $battle->user_id) ?>
<?php if ($battle->link_url != '' || $_editable) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Link')) ?></th>
            <td id="link-cell">
              <?= Html::beginTag('div', [
                'id' => 'link-cell-display',
                'data' => [
                  'post' => Url::to(['api-internal/patch-battle', 'id' => $battle->id]),
                  'url' => $battle->link_url,
                ],
              ]) . "\n" ?>
<?php if ($battle->link_url != '') { ?>
                <?= Html::a(
                  Html::encode($battle->link_url), //TODO: decode IDN
                  $battle->link_url,
                  ['rel' => 'nofollow']
                ) . "\n" ?>
<?php } ?>
<?php if ($_editable) { ?>
<?php BattleEditAsset::register($this) ?>
                <button id="link-cell-start-edit" class="btn btn-default btn-xs" disabled>
                  <?= FA::fas('pencil-alt')->fw() . "\n" ?>
                  <?= Html::encode(Yii::t('app', 'Edit')) . "\n" ?>
                </button>
<?php } ?>
              </div>
<?php if ($_editable) { ?>
              <div id="link-cell-edit" style="display:none">
                <div class="form-group-sm">
                  <input type="url" value="" id="link-cell-edit-input" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <?= Html::tag(
                  'button',
                  Html::encode(Yii::t('app', 'Apply')),
                  [
                    'type' => 'button',
                    'id' => 'link-cell-edit-apply',
                    'class' => 'btn btn-primary btn-xs',
                    'disabled' => null,
                    'data' => [
                      'error' => Yii::t('app', 'Could not be updated.'),
                    ],
                   ]
                ) . "\n" ?>
              </div>
<?php } ?>
            </td>
          </tr>
<?php } ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Battle Start')) ?></th>
            <td><?= ($battle->start_at)
              ? TimestampColumnWidget::widget([
                'value' => $battle->start_at,
                'showRelative' => true
              ])
              : '?'
            ?></td>
          </tr>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Battle End')) ?></th>
            <td><?= ($battle->end_at)
              ? TimestampColumnWidget::widget([
                'value' => $battle->end_at,
                'showRelative' => true
              ])
              : '?'
            ?></td>
          </tr>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Data Sent')) ?></th>
            <td>
<?php if ($battle->at && ($_t = strtotime($battle->at))) { ?>
              <?= TimestampColumnWidget::widget([
                'value' => $battle->at,
                'showRelative' => true
              ]) . "\n" ?>
<?php foreach (['dateCreated', 'dateModified', 'datePublished'] as $itemprop) { ?>
              <?= Html::tag('meta', '', [
                'itemprop' => $itemprop,
                'content' => gmdate('Y-m-d\TH:i:sP', $_t),
              ]) . "\n" ?>
<?php } ?>
<?php } ?>
            </td>
          </tr>
<?php if ($battle->agent) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'User Agent')) ?></th>
            <td><?= vsprintf('%s / %s', [
              $battle->agent->getProductUrl()
                ? Html::a(
                  Html::encode($battle->agent->name),
                  $battle->agent->getProductUrl(),
                  [
                    'target' => '_blank',
                    'rel' => 'nofollow noopener',
                  ]
                )
                : Html::encode($battle->agent->name),
              $battle->agent->getVersionUrl()
                ? Html::a(
                  Html::encode($battle->agent->version),
                  $battle->agent->getVersionUrl(),
                  [
                    'target' => '_blank',
                    'rel' => 'nofollow noopener',
                  ]
                )
                : Html::encode($battle->agent->version),
            ]) ?></td>
          </tr>
<?php } ?>
<?php if ($battle->ua_variables) { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Extra Data')) ?></th>
            <td>
              <table class="table mb-0">
                <tbody>
<?php foreach ($battle->extraData as $k => $v) { ?>
                  <tr>
                    <th scope="row"><?= Html::encode(Yii::t('app-ua-vars', $k)) ?></th>
                    <td><?= Html::encode(Yii::t('app-ua-vars-v', $v)) ?></td>
                  </tr>
<?php } ?>
                </tbody>
              </table>
            </td>
          </tr>
<?php } ?>
<?php if ($battle->note != '') { ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Note')) ?></th>
            <td><?= nl2br(Html::encode($battle->note)) ?></td>
          </tr>
<?php } ?>
<?php if (
  $battle->private_note != '' &&
  !Yii::$app->user->isGuest &&
  Yii::$app->user->identity->id == $user->id
) { ?>
<?php BattlePrivateNoteAsset::register($this) ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Note (private)')) ?></th>
            <td>
              <button class="btn btn-default" id="private-note-show">
                <?= (string)FA::fas('lock')->fw() . "\n" ?>
              </button>
              <div id="private-note" class="d-none">
                <?= nl2br(Html::encode($battle->private_note)) . "\n" ?>
              </div>
            </td>
          </tr>
<?php } ?>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Game Version')) ?></th>
            <td><?=
              ($battle->splatoonVersion)
                ? Html::encode($battle->splatoonVersion->name)
                : Html::encode(Yii::t('app', 'Unknown'))
            ?></td>
          </tr>
        </tbody>
      </table>
<?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $user->id) { ?>
      <p class="text-right"><?= Html::a(
        Html::encode(Yii::t('app', 'Edit')),
        ['show/edit-battle',
          'screen_name' => $user->screen_name,
          'battle' => $battle->id,
        ],
        ['class' => 'btn btn-default']
      ) ?></p>
<?php } ?>
<?php $hasExtendedData = false ?>
<?php if ($battle->myTeamPlayers && $battle->hisTeamPlayers) { ?>
<?php if ($battle->my_team_color_rgb && $battle->his_team_color_rgb) { ?>
<?php $this->registerCss(Html::renderCss([
  '#players .bg-my' => [
    'background' => '#' . $battle->my_team_color_rgb,
    'color' => '#fff',
    'text-shadow' => '1px 1px 0 rgba(0,0,0,.8)',
  ],
  '#players .bg-his' => [
    'background' => '#' . $battle->his_team_color_rgb,
    'color' => '#fff',
    'text-shadow' => '1px 1px 0 rgba(0,0,0,.8)',
  ],
])) ?>
<?php } ?>
<?php $this->registerCss(Html::renderCss(['#players .its-me' => ['background' => '#ffc']])) ?>
<?php
$hideRank = true;
$hidePoint = true;
if (!$battle->rule || $battle->rule->key !== 'nawabari') {
  $hideRank = false;
}
if (
  !$battle->rule ||
  ($battle->rule->key === 'nawabari' && (!$battle->lobby || $battle->lobby->key !== 'fest'))
) {
  $hidePoint = false;
}
$hasExtendedData = true;
?>
      <table class="table table-bordered" id="players">
        <thead>
          <tr>
            <th style="width:1em"></th>
            <th class="col-weapon"><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
            <th class="col-level"><?= Html::encode(Yii::t('app', 'Level')) ?></th>
<?php if (!$hideRank) { ?>
            <th class="col-rank"><?= Html::encode(Yii::t('app', 'Rank')) ?></th>
<?php } ?>
<?php if (!$hidePoint) { ?>
            <th class="col-point"><?= Html::encode(Yii::t('app', 'Points')) ?></th>
<?php } ?>
            <th class="col-kd"><?= Html::encode(vsprintf('%s/%s', [
              Yii::t('app', 'k'),
              Yii::t('app', 'd'),
            ])) ?></th>
            <?= Html::tag(
              'th',
              Html::encode(Yii::t('app', 'Ratio')),
              [
                'class' => 'col-kr auto-tooltip',
                'title' => Yii::t('app', 'Kill Ratio'),
              ]
            ) . "\n" ?>
            <?= Html::tag(
              'th',
              Html::encode(Yii::t('app', 'Rate')),
              [
                'class' => 'col-kr auto-tooltip',
                'title' => Yii::t('app', 'Kill Rate'),
              ]
            ) . "\n" ?>
          </tr>
        </thead>
        <tbody>
<?php
$teams = ($battle->is_win === false) ? ['his', 'my'] : ['my', 'his'];
$bonus = $battle->bonus->bonus ?? 300;
?>
<?php foreach ($teams as $i => $teamKey) { ?>
<?php
$attr = $teamKey . 'TeamPlayers';
$totalKill = 0;
$totalDeath = 0;
$totalPoint = 0;
$hasNull = false;
$hasMyKill = false;
foreach ($battle->$attr as $player) {
  if ($player->kill === null || $player->death === null) {
    $hasNull = true;
  } else {
    $totalKill = $totalKill + $player->kill;
    $totalDeath = $totalDeath + $player->death;
  }

  if ($totalPoint !== null && $player->point !== null) {
    $totalPoint = $totalPoint + $player->point;
    if ($i === 0) {
      // 勝利チーム側の合計からは勝利ボーナスを消す
      $totalPoint = $totalPoint - $bonus;
    }
  } else {
    $totalPoint = null;
  }
  if ($player->my_kill !== null) {
    $hasMyKill = true;
  }
}
?>
            <?= Html::beginTag('tr', ['class' => 'bg-' . $teamKey]) . "\n" ?>
              <th colspan="2"><?= ($teamKey === 'my')
                ? Html::encode(Yii::t('app', 'Good Guys'))
                : Html::encode(Yii::t('app', 'Bad Guys'))
              ?></th>
              <td></td>
<?php if (!$hideRank) { ?>
              <td></td>
<?php } ?>
<?php if (!$hidePoint) { ?>
              <td class="text-right"><?= ($totalPoint !== null)
                ? Html::encode(Yii::$app->formatter->asInteger($totalPoint))
                : ''
              ?></td>
<?php } ?>
              <td class="text-center"><?=
                ($hasNull)
                  ? ''
                  : Html::encode(vsprintf('%s / %s', [
                    Yii::$app->formatter->asInteger($totalKill),
                    Yii::$app->formatter->asInteger($totalDeath),
                  ]))
              ?></td>
              <td class="text-right"><?=
                ($hasNull || ($totalDeath == 0) && ($totalKill == 0))
                  ? ''
                  : Html::encode(Yii::$app->formatter->asDecimal(
                    ($totalDeath == 0)
                      ? 99.99
                      : ($totalKill / $totalDeath),
                    2
                  ))
              ?></td>
              <td class="text-right">
<?php if (!$hasNull && ($totalDeath > 0 || $totalKill > 0)) { ?>
                <?= Html::encode(Yii::$app->formatter->asPercent(
                  $totalKill / ($totalKill + $totalDeath),
                  1
                )) . "\n" ?>
<?php } ?>
              </td>
            </tr>
<?php foreach ($battle->$attr as $player) { ?>
            <?= Html::beginTag('tr', ['class' => $player->is_me ? 'its-me' : '']) . "\n" ?>
              <?= Html::tag('td', '', ['class' => "bg-{$teamKey}"]) . "\n" ?>
              <td class="col-weapon">
<?php if ($player->weapon) { ?>
                <?= Html::tag(
                  'span',
                  Html::encode(Yii::t('app-weapon', $player->weapon->name)),
                  [
                    'class' => 'auto-tooltip',
                    'title' => implode(' / ', [
                      implode('', [
                        Yii::t('app', 'Sub:'),
                        Yii::t('app-subweapon', $player->weapon->subweapon->name),
                      ]),
                      implode('', [
                        Yii::t('app', 'Special:'),
                        Yii::t('app-special', $player->weapon->special->name),
                      ]),
                    ]),
                  ]
                ) . "\n" ?>
<?php } ?>
              </td>
              <td class="col-level text-right"><?= Html::encode($player->level) ?></td>
<?php if (!$hideRank) { ?>
              <td class="col-rank text-center"><?= Html::encode(
                Yii::t('app-rank', $player->rank->name ?? '')
              ) ?></td>
<?php } ?>
<?php if (!$hidePoint) { ?>
              <td class="col-point text-right"><?= Html::encode(
                ($player->point !== null)
                  ? Yii::$app->formatter->asInteger($player->point)
                  : ''
              ) ?></td>
<?php } ?>
              <td class="col-kd text-center">
                <?= (($player->kill === null) ? '?' : Html::encode($player->kill)) . "\n" ?>
<?php if ($hasMyKill && $teamKey === 'his') { ?>
                (<?= ($player->my_kill === null) ? '?' : Html::encode($player->my_kill) ?>)
<?php } ?>
                /
                <?= (($player->death === null) ? '?' : Html::encode($player->death)) . "\n" ?>
                <?= KillRatioBadgeWidget::widget([
                  'kill' => $player->kill,
                  'death' => $player->death,
                ]) . "\n" ?>
              </td>
              <td class="col-kr text-right"><?php
                if ($player->death == 0) {
                  if ($player->kill > 0) {
                    echo Html::encode(Yii::$app->formatter->asDecimal(99.99, 2));
                  }
                } else {
                  echo Html::encode(Yii::$app->formatter->asDecimal(
                    $player->kill / $player->death,
                    2
                  ));
                }
              ?></td>
              <td class="col-kr text-right"><?php
                if ($player->kill !== null && $player->death !== null) {
                  if ($player->kill > 0 || $player->death > 0) {
                    echo Html::encode(Yii::$app->formatter->asPercent(
                      $player->kill / ($player->kill + $player->death),
                      1
                    ));
                  }
                }
              ?></td>
            </tr>
<?php } ?>
<?php } ?>
        </tbody>
      </table>
<?php } ?>
<?php if ($battle->events) { ?>
<?php $events = is_array($battle->events) ? $battle->events : @json_decode($battle->events, false) ?>
<?php if ($events) { ?>
<?php
BattleTimelineAsset::register($this);
$hasExtendedData = true;
$this->registerJsVar('mySpecial', $battle->weapon->special->key ?? null);
$this->registerJsVar('battleEvents', $events);
$this->registerJsVar('deathReasons', $battle->getDeathReasonNamesFromEvents());
?>
          <div id="timeline-legend">
          </div>
          <div class="graph" id="timeline">
          </div>
<?php if ($battle->rule && $battle->isGachi) { ?>
          <p class="text-right">
            <label>
              <?= Html::tag('input', '', [
                'checked' => $battle->rule->key === 'area',
                'disabled' => true,
                'id' => 'enable-smoothing',
                'type' => 'checkbox',
              ]) . "\n" ?>
<?php if ($battle->rule->key === 'yagura' || $battle->rule->key === 'hoko') { ?>
              <?= Html::encode(
                Yii::t('app', 'Enable noise reduction (position of the objective)')
              ) . "\n" ?>
<?php } elseif ($battle->rule->key === 'area') { ?>
              <?= Html::encode(
                Yii::t('app', 'Enable noise reduction (count)')
              ) . "\n" ?>
<?php } ?>
            </label>
          </p>
<?php } ?>
<?php
$iconLoad = function (string $path): JsExpression {
  $am = Yii::$app->assetManager;
  return new JsExpression(vsprintf('imgLoad(%s)', [
    Json::encode($am->getAssetUrl($am->getBundle(GraphIconAsset::class), $path)),
  ]));
};
$this->registerJsVar('imgLoad', new JsExpression(
  'function(n){var r=new Image;return r.src=n,r}'
));
$this->registerJsVar('graphIcon', [
  'dead' => $iconLoad('dead/default.png'),
  'deadSp' => $iconLoad('dead/special.png'),
  'killed' => $iconLoad('killed/default.png'),
  'lowInk' => $iconLoad('low_ink.png'),
  'specialCharged' => $iconLoad('special_charged.png'),
  'specials' => ArrayHelper::map(
    Special::find()->asArray()->all(),
    'key',
    function (array $item) use ($iconLoad): JsExpression {
      return $iconLoad('specials/' . $item['key'] . '.png');
    }
  ),
]);
if ($battle->rule) {
  $this->registerJsVar('isNawabari', ($battle->rule->key === 'nawabari'));
  $this->registerJsVar('isGachi', !($battle->rule->key === 'nawabari'));
  $this->registerJsVar('ruleKey', $battle->rule->key);
} else {
  $this->registerJsVar('isNawabari', false);
  $this->registerJsVar('isGachi', false);
  $this->registerJsVar('ruleKey', null);
}
$this->registerJsVar('myTeamColorHue', $battle->my_team_color_hue);
$this->registerJsVar('hisTeamColorHue', $battle->his_team_color_hue);

$js = <<<'END'
Array.prototype.sum||(Array.prototype.sum=function(){return this.reduce(function(t,r){return t+r},0)});
Array.prototype.avg||(Array.prototype.avg=function(){return 0<this.length?this.sum()/this.length:NaN});
END;
$this->registerJs($js, View::POS_BEGIN);
$this->registerJsVar('specialNames', ArrayHelper::map(
  Special::find()->orderBy('key')->all(),
  'key',
  function (Special $sp): string {
    return Yii::t('app-special', $sp->name);
  }
));
$this->registerJsVar('timelineTranslates', [
  'badGuys' => Yii::t('app', 'Bad Guys'),
  'combos' => Yii::t('app', 'combos'),
  'controlBad' => Yii::t('app', 'Bad guys are in control'),
  'controlGood' => Yii::t('app', 'Good guys are in control'),
  'controlNoOne' => Yii::t('app', 'No one in control'),
  'countBad' => Yii::t('app', 'Count (Bad Guys)'),
  'countGood' => Yii::t('app', 'Count (Good Guys)'),
  'goodGuys' => Yii::t('app', 'Good Guys'),
  'lowInk' => Yii::t('app', 'Low ink'),
  'neutral' => Yii::t('app', 'Neutral'),
  'position' => Yii::t('app', 'Position'),
  'spCharged' => Yii::t('app', 'Special Charged'),
  'specialPct' => Yii::t('app', 'Special %'),
  'streak' => Yii::t('app', 'streak'),
  'turfInked' => Yii::t('app', 'Turf Inked'),
  'winningBad' => Yii::t('app', 'Bad guys winning'),
  'winningGood' => Yii::t('app', 'Good guys winning'),
]);
?>
<?php } ?>
<?php } ?>

<?php
$effects = trim($this->render('_battle_ability_effect', ['battle' => $battle]));
if ($effects) {
  $hasExtendedData = true;
  echo $effects . "\n";
}
?>
<?php if (
  !Yii::$app->user->isGuest &&
  Yii::$app->user->identity->id == $user->id &&
  $hasExtendedData
) { ?>
      <p class="text-right"><?= Html::a(
          Html::encode(Yii::t('app', 'Edit')),
          ['show/edit-battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
          ['class' => 'btn btn-default']
      ) ?></p>
<?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= $this->render("//includes/user-miniinfo", ["user" => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
  <span itemscope itemprop="publisher" itemtype="http://schema.org/Organization">
    <meta itemprop="name" content="<?= Html::encode(Yii::$app->name) ?>">
    <meta itemprop="url" content="<?= Html::encode(Url::to(['site/index'], true)) ?>">
    <span itemscope itemprop="logo" itemtype="http://schema.org/ImageObject">
      <?= Html::tag('meta', '', [
        'itemprop' => 'url',
        'itemtype' => 'http://schema.org/URL',
        'content' => Url::to(
          Yii::$app->getAssetManager()->getAssetUrl(
            Yii::$app->getAssetManager()->getBundle(AppAsset::class),
            'favicon.png'
          ),
          true
        ),
      ]) . "\n" ?>
      <meta itemprop="width" content="512">
      <meta itemprop="height" content="512">
    </span>
<?php if (Yii::$app->name === 'stat.ink') { ?>
    <span itemscope itemprop="member" itemtype="http://schema.org/Person">
      <meta itemprop="familyName" content="Aizawa">
      <meta itemprop="givenName" content="Hina">
      <meta itemprop="url" content="https://fetus.jp/">
      <meta itemprop="email" content="hina@fetus.jp">
    </span>
    <span itemscope itemprop="funder" itemtype="http://schema.org/Organization">
      <meta itemprop="name" content="さくらインターネット">
      <meta itemprop="url" content="https://www.sakura.ad.jp/">
      <?= Html::tag('meta', '', [
        'itemprop' => 'logo',
        'itemtype' => 'http://schema.org/URL',
        'content' => 'https://www.sakura.ad.jp/resource/images/header_logo_bgwhite.png',
      ]) . "\n"?>
    </span>
<?php } ?>
  </span>
</div>
