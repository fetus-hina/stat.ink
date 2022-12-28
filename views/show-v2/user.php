<?php

declare(strict_types=1);

use app\assets\BattleListAsset;
use app\assets\BattleListGroupHeaderAsset;
use app\assets\Spl2WeaponAsset;
use app\components\grid\KillRatioColumn;
use app\components\helpers\Battle as BattleHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Battle2FilterWidget;
use app\components\widgets\EmbedVideo;
use app\components\widgets\FA;
use app\components\widgets\GameModeIcon;
use app\components\widgets\Icon;
use app\components\widgets\Label;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo2;
use app\models\Battle2;
use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var User $user
 * @var View $this
 */

BattleListAsset::register($this);

$title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag([
  'name' => 'twitter:image',
  'content' => $user->iconUrl,
]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => sprintf('@%s', $user->twitter)]);
}
?>
<div class="container">
  <span itemscope itemtype="http://schema.org/BreadcrumbList">
    <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
      <?= Html::tag('meta', '', ['itemprop' => 'url', 'content' => Url::home(true)]) . "\n" ?>
      <?= Html::tag('meta', '', ['itemprop' => 'title', 'content' => Yii::$app->name]) . "\n" ?>
    </span>
  </span>
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= SnsWidget::widget([
    'tweetText' => (function () use ($title, $summary) {
      $fmt = Yii::$app->formatter;
      return sprintf(
        '%s [ %s ]',
        $title,
        Yii::t('app', 'Battles:{0} / Win %:{1} / Avg Kills:{2} / Avg Deaths:{3} / Kill Ratio:{4}', [
          $fmt->asInteger($summary->battle_count),
          $summary->wp === null ? '-' : $fmt->asPercent($summary->wp / 100, 1),
          $summary->kd_present > 0
            ? $fmt->asDecimal($summary->total_kill / $summary->kd_present, 2)
            : '-',
          $summary->kd_present > 0
            ? $fmt->asDecimal($summary->total_death / $summary->kd_present, 2)
            : '-',
          $summary->kd_present > 0
            ? ($summary->total_death == 0
              ? ($summary->total_kill == 0 ? '-' : '∞')
              : $fmt->asDecimal($summary->total_kill / $summary->total_death, 2)
            )
            : '-',
        ])
      );
    })(),
    'feedUrl' => Url::to(
        ['feed/user-v2',
            'screen_name' => $user->screen_name,
            'type' => 'rss',
            'lang' => preg_replace('/@.+$/', '', Yii::$app->language),
        ],
        true
    ),
    'jsonUrl' => ['api-v2-battle/index', 'screen_name' => $user->screen_name],
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <p class="text-right">
        <?= Html::a(
          implode(' ', [
            FA::fas('fish')->fw(),
            Html::encode(Yii::t('app-salmon2', 'Salmon Run')),
            Icon::subPage(),
          ]),
          ['salmon/index', 'screen_name' => $user->screen_name],
          ['class' => 'btn btn-default btn-xs']
        ) . "\n" ?>
      </p>
      <div class="text-center">
        <?= ListView::widget([
          'dataProvider' => $battleDataProvider,
          'itemOptions' => [
            'tag' => false,
          ],
          'layout' => '{pager}',
          'pager' => [
            'maxButtonCount' => 5
          ],
        ]) . "\n" ?>
      </div>
      <?= $this->render('/includes/battles-summary', [
        'headingText' => Yii::t('app', 'Summary: Based on the current filter'),
        'summary' => $summary
      ]) . "\n" ?>
      <div style="margin-bottom:10px">
        <a href="#filter-form" class="visible-xs-inline-block btn btn-info">
          <?= Icon::search() . "\n" ?>
          <?= Html::encode(Yii::t('app', 'Search')) . "\n" ?>
        </a>
        <a href="#table-config" class="btn btn-default">
          <?= FA::fas('cogs')->fw() . "\n" ?>
          <?= Html::encode(Yii::t('app', 'View Settings')) . "\n" ?>
        </a>
        <?= Html::a(
          implode(' ', [
            (string)FA::fas('list')->fw(),
            Html::encode(Yii::t('app', 'Simplified List')),
          ]),
          array_merge(
            $filter->toQueryParams(),
            ['show-v2/user', 'screen_name' => $user->screen_name, 'v' => 'simple'],
          ),
          ['class' => 'btn btn-default', 'rel' => 'nofollow']
        ) . "\n" ?>
      </div>
      <?= GridView::widget([
        'options' => [
          'id' => 'battles',
          'class' => 'table-responsive',
        ],
        'layout' => '{items}',
        'dataProvider' => $battleDataProvider,
        'tableOptions' => [
          'class' => 'table table-striped table-condensed'
        ],
        'rowOptions' => function (Battle2 $model): array {
          return [
            'class' => [
              'battle-row',
              $model->getHasDisconnectedPlayer() ? 'disconnected' : '',
            ],
          ];
        },
        'beforeRow' => function (Battle2 $model, int $key, int $index, GridView $widget): ?string {
          static $lastPeriod = null;
          if ($lastPeriod !== $model->period) {
            $lastPeriod = $model->period;
            $fmt = Yii::$app->formatter;
            list($from, $to) = BattleHelper::periodToRange2DT($model->period);
            BattleListGroupHeaderAsset::register($this);
            return Html::tag('tr', Html::tag(
              'td',
              implode(' - ', [
                Html::tag(
                  'time',
                  Html::encode(implode(' ', array_filter([
                    $fmt->asDate($from, 'medium'),
                    $fmt->asTime($from, 'short'),
                  ]))),
                  [
                    'datetime' => $from->setTimezone(new DateTimeZone('Etc/UTC'))
                      ->format(DateTime::ATOM),
                  ]
                ),
                Html::tag(
                  'time',
                  Html::encode(implode(' ', array_filter([
                    $fmt->asDate($from, 'medium') !== $fmt->asDate($to, 'medium')
                      ? $fmt->asDate($to, 'medium')
                      : null,
                    $fmt->asTime($to, 'short'),
                  ]))),
                  [
                    'datetime' => $to->setTimezone(new DateTimeZone('Etc/UTC'))
                      ->format(DateTime::ATOM),
                  ]
                ),
              ]),
              [
                'class' => 'battle-row-group-header',
                'colspan' => (string)count($widget->columns),
              ]
            ));
          }
          return null;
        },
        'columns' => [ // {{{
          [
            // button {{{
            'format' => 'raw',
            'value' => function ($model): string {
              return trim(implode(' ', [
                Html::a(
                  Yii::t('app', 'Detail'),
                  ['show-v2/battle', 'screen_name' => $model->user->screen_name, 'battle' => $model->id],
                  ['class' => 'btn btn-primary btn-xs']
                ),
                (!$model->link_url)
                  ? ''
                  : Html::a(
                    (string)FA::fas(EmbedVideo::isSupported($model->link_url) ? 'video' : 'link')->fw(),
                    $model->link_url,
                    ['class' => 'btn btn-default btn-xs', 'rel' => 'nofollow']
                  ),
              ]));
            },
            'contentOptions' => ['class' => 'nobr'],
            // }}}
          ],
          [
            // battle # {{{
            'label' => Yii::t('app', '#'),
            'attribute' => 'splatnet_number',
            'headerOptions' => ['class' => 'cell-splatnet'],
            'contentOptions' => ['class' => 'cell-splatnet'],
            'format' => 'integer',
            // }}}
          ],
          [
            // lobby (icon) {{{
            'label' => Html::tag(
              'span',
              Html::encode(Yii::t('app', 'Lobby')),
              ['class' => 'sr-only']
            ),
            'encodeLabel' => false,
            'headerOptions' => ['class' => 'cell-lobby-icon'],
            'contentOptions' => ['class' => 'cell-lobby-icon'],
            'format' => 'raw',
            'value' => function (Battle2 $model): ?string {
              $f = function (string $rule, string $icon): string {
                return Html::tag(
                  'span',
                  GameModeIcon::spl2($icon, [
                    'style' => [
                      'height' => '1.2em',
                    ],
                  ]),
                  [
                    'class' => 'auto-tooltip',
                    'title' => Yii::t('app-rule2', $rule),
                  ]
                );
              };
              switch ($model->mode->key ?? '') {
                default:
                  return null;

                case 'regular':
                  return $f($model->mode->name, 'nawabari');

                case 'fest':
                  switch ($model->lobby->key ?? '') {
                    case 'standard':
                      if ($model->version) {
                        return $f(
                          version_compare($model->version->tag, '4.0.0', '<')
                            ? 'Splatfest (Solo)'
                            : 'Splatfest (Pro)',
                          'fest'
                        );
                      }
                      return $f('Splatfest (Pro/Solo)', 'fest');

                    case 'fest_normal':
                      return $f('Splatfest (Normal)', 'fest');

                    case 'squad_4':
                      return $f('Splatfest (Team)', 'fest');
                  
                    default:
                      return $f('Splatfest', 'fest');
                  }

                case 'gachi':
                  switch ($model->lobby->key ?? '') {
                    case 'standard':
                      return $f('Ranked Battle (Solo)', 'gachi');

                    case 'squad_2':
                      return $f('League Battle (Twin)', 'league');

                    case 'squad_4':
                      return $f('League Battle (Quad)', 'league');

                    default:
                      return $f('Ranked Battle', 'gachi');
                  }

                case 'private':
                  return $f('Private Battle', 'private');
              }
            },
            // }}}
          ],
          [
            // lobby {{{
            'label' => Yii::t('app', 'Lobby'),
            'headerOptions' => ['class' => 'cell-lobby'],
            'contentOptions' => ['class' => 'cell-lobby'],
            'value' => function ($model) {
              switch ($model->mode->key ?? '') {
                default:
                  return '?';

                case 'regular':
                  return Yii::t('app-rule2', $model->mode->name);

                case 'fest':
                  switch ($model->lobby->key ?? '') {
                    case 'standard':
                      if ($model->version) {
                        if (version_compare($model->version->tag, '4.0.0', '<')) {
                          return Yii::t('app-rule2', 'Splatfest (Solo)');
                        } else {
                          return Yii::t('app-rule2', 'Splatfest (Pro)');
                        }
                      }
                      return Yii::t('app-rule2', 'Splatfest (Pro/Solo)');

                    case 'fest_normal':
                      return Yii::t('app-rule2', 'Splatfest (Normal)');

                    case 'squad_4':
                      return Yii::t('app-rule2', 'Splatfest (Team)');
                  
                    default:
                      return Yii::t('app-rule2', 'Splatfest');
                  }

                case 'gachi':
                  switch ($model->lobby->key ?? '') {
                    case 'standard':
                      return Yii::t('app-rule2', 'Ranked Battle (Solo)');

                    case 'squad_2':
                      return Yii::t('app-rule2', 'League Battle (Twin)');

                    case 'squad_4':
                      return Yii::t('app-rule2', 'League Battle (Quad)');

                    default:
                      return Yii::t('app-rule2', 'Ranked Battle');
                  }

                case 'private':
                  return Yii::t('app-rule2', 'Private Battle');
              }
            },
            // }}}
          ],
          [
            // private room id (icon) {{{
            'label' => Yii::t('app', 'Room'),
            'headerOptions' => ['class' => 'cell-room cell-room-id'],
            'contentOptions' => ['class' => 'cell-room cell-room-id text-center'],
            'format' => 'raw',
            'value' => function ($model): ?string {
              if (!$model->lobby || $model->lobby->key !== 'private') {
                return null;
              }

              if (!$roomId = $model->privateRoomId) {
                return null;
              }

              return Html::img(
                sprintf('%s/%s.svg', Yii::getAlias('@jdenticon'), $model->privateRoomId),
                [
                  'title' => substr($roomId, 0, 16),
                  'class' => 'auto-tooltip',
                  'style' => [
                    'width' => 'auto',
                    'height' => '1.5em',
                  ],
                ]
              );
            },
            // }}}
          ],
          [
            // private room id (icon) {{{
            'label' => Yii::t('app', 'Team'),
            'headerOptions' => ['class' => 'cell-room cell-room-team'],
            'contentOptions' => ['class' => 'cell-room cell-room-team text-center'],
            'format' => 'raw',
            'value' => function ($model): ?string {
              if (!$model->lobby || $model->lobby->key !== 'private') {
                return null;
              }

              $id1 = $model->getPrivateMyTeamId();
              $id2 = $model->getPrivateHisTeamId();
              if (!$id1 || !$id2) {
                return null;
              }

              return implode(
                ' ',
                array_map(
                  function (string $id): string {
                    return Html::img(
                      sprintf('%s/%s.svg', Yii::getAlias('@jdenticon'), rawurlencode($id)),
                      [
                        'title' => substr($id, 0, 16),
                        'class' => 'auto-tooltip',
                        'style' => [
                          'width' => 'auto',
                          'height' => '1.5em',
                        ],
                      ]
                    );
                  },
                  [$id1, $id2]
                )
              );
            },
            // }}}
          ],
          [
            // mode (icon) {{{
            'label' => Html::tag(
              'span',
              Html::encode(Yii::t('app', 'Mode')),
              ['class' => 'sr-only']
            ),
            'encodeLabel' => false,
            'headerOptions' => ['class' => 'cell-rule-icon'],
            'contentOptions' => ['class' => 'cell-rule-icon'],
            'format' => 'raw',
            'value' => function (Battle2 $model): ?string {
              if (!$model->rule) {
                return null;
              }

              return Html::tag(
                'span',
                GameModeIcon::spl2($model->rule->key, [
                  'style' => [
                    'height' => '1.2em',
                  ],
                ]),
                [
                  'class' => 'auto-tooltip',
                  'title' => Yii::t('app-rule2', $model->rule->name),
                ]
              );
            },
            // }}}
          ],
          [
            // mode {{{
            'label' => Yii::t('app', 'Mode'),
            'attribute' => 'rule.name',
            'headerOptions' => ['class' => 'cell-rule'],
            'contentOptions' => ['class' => 'cell-rule'],
            'format' => ['translated', 'app-rule2'],
            // }}}
          ],
          [
            // mode (short) {{{
            'label' => Yii::t('app', 'Mode'),
            'headerOptions' => ['class' => 'cell-rule-short'],
            'contentOptions' => ['class' => 'cell-rule-short'],
            'format' => 'raw',
            'value' => function ($model): string {
              return Html::tag(
                'span',
                Html::encode(Yii::t('app-rule2', $model->rule->short_name ?? '?')),
                [
                  'class' => 'auto-tooltip',
                  'title' => Yii::t('app-rule2', $model->rule->name ?? '?'),
                ]
              );
            },
            // }}}
          ],
          [
            // special battle {{{
            'label' => Yii::t('app', 'Special Battle'),
            'headerOptions' => ['class' => 'cell-special-battle'],
            'contentOptions' => ['class' => 'cell-special-battle'],
            'format' => 'raw',
            'value' => function ($model): ?string {
              if (!$model->special_battle_id || !$model->specialBattle) {
                return null;
              }
              return Label::widget([
                'content' => Yii::t('app', $model->specialBattle->name),
                'color' => 'default',
              ]);
            },
            // }}}
          ],
          [
            // stage {{{
            'label' => Yii::t('app', 'Stage'),
            'attribute' => 'map.name',
            'headerOptions' => ['class' => 'cell-map'],
            'contentOptions' => ['class' => 'cell-map'],
            'format' => ['translated', 'app-map2'],
            // }}}
          ],
          [
            // stage (short) {{{
            'label' => Yii::t('app', 'Stage'),
            'headerOptions' => ['class' => 'cell-map-short'],
            'contentOptions' => ['class' => 'cell-map-short'],
            'format' => 'raw',
            'value' => function ($model): string {
              return Html::tag(
                'span',
                Html::encode(Yii::t('app-map2', $model->map->short_name ?? '?')),
                [
                  'class' => 'auto-tooltip',
                  'title' => Yii::t('app-map2', $model->map->name ?? '?'),
                ]
              );
            },
            // }}}
          ],
          [
            // weapon (icon) {{{
            'label' => '', // Yii::t('app', 'Weapon'),
            'headerOptions' => ['class' => 'cell-main-weapon-icon'],
            'contentOptions' => ['class' => 'cell-main-weapon-icon'],
            'format' => 'raw',
            'value' => function ($model): ?string {
              if (!$model->weapon) {
                return null;
              }

              $icons = Spl2WeaponAsset::register($this);
              return Html::img(
                $icons->getIconUrl($model->weapon->key),
                [
                  'style' => [
                    'height' => '1.5em',
                    'width' => 'auto',
                  ],
                  'class' => 'auto-tooltip',
                  'title' => Yii::t('app-weapon2', $model->weapon->name),
                ]
              );
            },
            // }}}
          ],
          [
            // weapon {{{
            'label' => Yii::t('app', 'Weapon'),
            'headerOptions' => ['class' => 'cell-main-weapon'],
            'contentOptions' => ['class' => 'cell-main-weapon'],
            'format' => 'raw',
            'value' => function ($model): string {
              $title = implode(' / ', [
                implode(' ', [
                  Yii::t('app', 'Sub:'),
                  Yii::t('app-subweapon2', $model->weapon->subweapon->name ?? '?'),
                ]),
                implode(' ', [
                  Yii::t('app', 'Special:'),
                  Yii::t('app-special2', $model->weapon->special->name ?? '?'),
                ]),
              ]);
              return Html::tag(
                'span',
                Html::encode(Yii::t('app-weapon2', $model->weapon->name ?? '?')),
                ['class' => 'auto-tooltip', 'title' => $title]
              );
            },
            // }}}
          ],
          [
            // weapon (short) {{{
            'label' => Yii::t('app', 'Weapon'),
            'headerOptions' => ['class' => 'cell-main-weapon-short'],
            'contentOptions' => ['class' => 'cell-main-weapon-short'],
            'format' => 'raw',
            'value' => function ($model): string {
              $title = implode(' / ', [
                Yii::t('app-weapon2', $model->weapon->name ?? '?'),
                implode(' ', [
                  Yii::t('app', 'Sub:'),
                  Yii::t('app-subweapon2', $model->weapon->subweapon->name ?? '?'),
                ]),
                implode(' ', [
                  Yii::t('app', 'Special:'),
                  Yii::t('app-special2', $model->weapon->special->name ?? '?'),
                ]),
              ]);
              return Html::tag(
                'span',
                Html::encode(Yii::$app->weaponShortener->get(
                  Yii::t('app-weapon2', $model->weapon->name ?? '?')
                )),
                [
                  'class' => 'auto-tooltip',
                  'title' => $title,
                ]
              );
            },
            // }}}
          ],
          [
            // freshness {{{
            'label' => Yii::t('app', 'Freshness'),
            'headerOptions' => ['class' => 'cell-freshness'],
            'contentOptions' => ['class' => 'cell-freshness nobr'],
            'format' => 'raw',
            'value' => function ($model): ?string {
              if ($model->freshness === null) {
                return null;
              }

              $flag = null;
              if ($model->freshnessModel) {
                $flag = Html::tag(
                  'span',
                  (string)FA::fas('flag')->fw(),
                  [
                    'class' => [
                      'freshness-flag',
                      'freshness-flag-' . $model->freshnessModel->color,
                      'auto-tooltip',
                    ],
                    'title' => Yii::t('app-freshness2', $model->freshnessModel->name),
                  ]
                );
              }

              return trim(implode(' ', [
                $flag,
                Html::encode(Yii::$app->formatter->asDecimal($model->freshness, 1)),
              ]));
            },
            // }}}
          ],
          [
            // sub weapon (icon) {{{
            'label' => Html::tag(
              'span',
              Html::encode(Yii::t('app', 'Sub Weapon')),
              ['class' => 'sr-only']
            ),
            'encodeLabel' => false,
            'headerOptions' => ['class' => 'cell-sub-weapon-icon'],
            'contentOptions' => ['class' => 'cell-sub-weapon-icon'],
            'format' => 'raw',
            'value' => function (Battle2 $model): ?string {
              if (!$model->weapon || !$model->weapon->subweapon) {
                return null;
              }

              $icons = Spl2WeaponAsset::register($this);
              return Html::img(
                $icons->getIconUrl('sub/' . $model->weapon->subweapon->key),
                [
                  'style' => [
                    'height' => '1.333em',
                    'width' => 'auto',
                  ],
                  'class' => 'auto-tooltip',
                  'title' => Yii::t('app-subweapon2', $model->weapon->subweapon->name),
                ]
              );
            },
            // }}} 
          ],
          [
            // sub weapon {{{
            'label' => Yii::t('app', 'Sub Weapon'),
            'attribute' => 'weapon.subweapon.name',
            'headerOptions' => ['class' => 'cell-sub-weapon'],
            'contentOptions' => ['class' => 'cell-sub-weapon'],
            'format' => ['translated', 'app-subweapon2'],
            // }}} 
          ],
          [
            // special weapon (icon) {{{
            'label' => Html::tag(
              'span',
              Html::encode(Yii::t('app', 'Special Weapon')),
              ['class' => 'sr-only']
            ),
            'encodeLabel' => false,
            'headerOptions' => ['class' => 'cell-special-icon'],
            'contentOptions' => ['class' => 'cell-special-icon'],
            'format' => 'raw',
            'value' => function (Battle2 $model): ?string {
              if (!$model->weapon || !$model->weapon->special) {
                return null;
              }

              $icons = Spl2WeaponAsset::register($this);
              return Html::img(
                $icons->getIconUrl('sp/' . $model->weapon->special->key),
                [
                  'style' => [
                    'height' => '1.333em',
                    'width' => 'auto',
                  ],
                  'class' => 'auto-tooltip',
                  'title' => Yii::t('app-special2', $model->weapon->special->name),
                ]
              );
            },
            // }}} 
          ],
          [
            // special weapon {{{
            'label' => Yii::t('app', 'Special'),
            'attribute' => 'weapon.special.name',
            'headerOptions' => ['class' => 'cell-special'],
            'contentOptions' => ['class' => 'cell-special'],
            'format' => ['translated', 'app-special2'],
            // }}}
          ],
          [
            // team id (icon) {{{
            'label' => Yii::t('app', 'Team'),
            'headerOptions' => ['class' => 'cell-team-icon'],
            'contentOptions' => ['class' => 'cell-team-icon text-center'],
            'format' => 'raw',
            'value' => function ($model): ?string {
              return trim((string)$model->my_team_id) === ''
                ? null
                : Html::a(
                  Html::img(
                    $model->myTeamIcon,
                    [
                      'title' => $model->my_team_id,
                      'class' => 'auto-tooltip',
                      'style' => [
                        'width' => 'auto',
                        'height' => '1.5em',
                      ],
                    ]
                  ),
                  ['show-v2/user',
                    'screen_name' => $model->user->screen_name,
                    'filter' => [
                      'filter' => "team:{$model->my_team_id}",
                    ],
                  ]
                );
            },
            // }}}
          ],
          [
            // team id {{{
            'label' => Yii::t('app', 'Team ID'),
            'headerOptions' => ['class' => 'cell-team-id'],
            'contentOptions' => ['class' => 'cell-team-id'],
            'format' => 'raw',
            'value' => function ($model): ?string {
              return trim((string)$model->my_team_id) === ''
                ? null
                : Html::a(
                  Html::tag('code', Html::encode(trim((string)$model->my_team_id))),
                  ['show-v2/user',
                    'screen_name' => $model->user->screen_name,
                    'filter' => [
                      'filter' => "team:{$model->my_team_id}",
                    ],
                  ]
                );
            },
            // }}}
          ],
          [
            // rank {{{
            'label' => Yii::t('app', 'Rank'),
            'headerOptions' => ['class' => 'cell-rank'],
            'contentOptions' => ['class' => 'cell-rank'],
            'value' => function ($model): ?string {
              if (!$rank = $model->rank) {
                return null;
              }
              if ($rank->key === 's+' && $model->rank_exp !== null) {
                return sprintf(
                  '%s %d',
                  Yii::t('app-rank2', $rank->name),
                  $model->rank_exp
                );
              }
              return Yii::t('app-rank2', $rank->name);
            },
            // }}}
          ],
          [
            // x power {{{
            'label' => Yii::t('app', 'X Power'),
            'headerOptions' => ['class' => 'cell-x-power'],
            'contentOptions' => ['class' => 'cell-x-power'],
            'value' => function ($model): ?string {
              $rank = $model->rank;
              if ($rank && $rank->key === 'x' && $model->x_power !== null) {
                return Yii::$app->formatter->asDecimal($model->x_power, 1);
              }

              return null;
            },
            // }}}
          ],
          [
            // rank (after) {{{
            'label' => Yii::t('app', 'Rank (After)'),
            'headerOptions' => ['class' => 'cell-rank-after'],
            'contentOptions' => ['class' => 'cell-rank-after'],
            'value' => function ($model): ?string {
              if (!$rank = $model->rankAfter) {
                return null;
              }
              if ($rank->key === 's+' && $model->rank_after_exp !== null) {
                return sprintf(
                  '%s %d',
                  Yii::t('app-rank2', $rank->name),
                  $model->rank_after_exp
                );
              }
              return Yii::t('app-rank2', $rank->name);
            },
            // }}}
          ],
          [
            // x power (after) {{{
            'label' => Yii::t('app', 'X Power (after)'),
            'headerOptions' => ['class' => 'cell-x-power'],
            'contentOptions' => ['class' => 'cell-x-power'],
            'value' => function ($model): ?string {
              $rank = $model->rankAfter;
              if ($rank && $rank->key === 'x' && $model->x_power_after !== null) {
                return Yii::$app->formatter->asDecimal($model->x_power_after, 1);
              }

              return null;
            },
            // }}}
          ],
          [
            // gachi power {{{
            'label' => Yii::t('app', 'Power Level'),
            'headerOptions' => ['class' => 'cell-gachi-power'],
            'contentOptions' => ['class' => 'cell-gachi-power text-right'],
            'format' => 'integer',
            'value' => function ($model): ?int {
              if ($model->estimate_gachi_power < 1) {
                return null;
              }
              return (int)$model->estimate_gachi_power;
            },
            // }}}
          ],
          [
            // league power {{{
            'label' => Yii::t('app', 'League Power'),
            'headerOptions' => ['class' => 'cell-league-power'],
            'contentOptions' => ['class' => 'cell-league-power text-right'],
            'value' => function ($model): ?string {
              if ($model->league_point < 1) {
                return null;
              }
              return $model->league_point;
            },
            // }}}
          ],
          [
            // fest title {{{
            'label' => Yii::t('app', 'Splatfest Title'),
            'headerOptions' => ['class' => 'cell-fest-title'],
            'contentOptions' => ['class' => 'cell-fest-title'],
            'value' => function ($model): ?string {
              if (!$model->festTitle) {
                return null;
              }
              $gender = $model->gender;
              $theme = $model->myTeamFestTheme;
              $themeName = $theme->name ?? '***';
              $name = Yii::t('app-fest', $model->festTitle->getName($gender), [$themeName, $themeName]);
              return ($model->festTitle->key === 'king' || $model->fest_exp === null)
                ? $name
                : "{$name} {$model->fest_exp}";
            },
            // }}}
          ],
          [
            // fest title (after) {{{
            'label' => Yii::t('app', 'Splatfest Title (After)'),
            'headerOptions' => ['class' => 'cell-fest-title-after'],
            'contentOptions' => ['class' => 'cell-fest-title-after'],
            'value' => function ($model): ?string {
              if (!$model->festTitleAfter) {
                return null;
              }
              $gender = $model->gender;
              $theme = $model->myTeamFestTheme;
              $themeName = $theme->name ?? '***';
              $name = Yii::t('app-fest', $model->festTitleAfter->getName($gender), [$themeName, $themeName]);
              return ($model->festTitleAfter->key === 'king' || $model->fest_exp_after === null)
                ? $name
                : "{$name} {$model->fest_exp_after}";
            },
            // }}}
          ],
          [
            // fest power {{{
            'label' => Yii::t('app', 'Splatfest Power'),
            'headerOptions' => ['class' => 'cell-fest-power'],
            'contentOptions' => ['class' => 'cell-fest-power text-right'],
            'format' => ['decimal', 1],
            'value' => function (Battle2 $model): ?float {
              return $model->fest_power < 0.1 ? null : (float)$model->fest_power;
            },
            // }}}
          ],
          [
            // level {{{
            'label' => Yii::t('app', 'Level'),
            'headerOptions' => ['class' => 'cell-level'],
            'contentOptions' => ['class' => 'cell-level'],
            'format' => 'integer',
            'attribute' => 'level',
            // }}}
          ],
          [
            // judge {{{
            'label' => Yii::t('app', 'Judge'),
            'headerOptions' => ['class' => 'cell-judge'],
            'contentOptions' => ['class' => 'cell-judge'],
            'format' => 'raw',
            'value' => function ($model): string {
              return $this->render('_battle_judge', ['model' => $model]);
            },
            // }}}
          ],
          [
            // result {{{
            'label' => Yii::t('app', 'Result'),
            'headerOptions' => ['class' => 'cell-result'],
            'contentOptions' => ['class' => 'cell-result'],
            'format' => 'raw',
            'value' => function ($model): string {
              $parts = [
                ($model->is_win === null)
                  ? Html::encode('?')
                  : ($model->is_win
                    ? Html::tag('span', Html::encode(Yii::t('app', 'Won')), ['class' => 'label label-success'])
                    : Html::tag('span', Html::encode(Yii::t('app', 'Lost')), ['class' => 'label label-danger'])
                  ),
                ($model->isGachi && $model->is_knockout !== null)
                  ? ($model->is_knockout
                    ? Html::tag('span', Html::encode(Yii::t('app', 'K.O.')), [
                        'class' => 'label label-info auto-tooltip',
                        'title' => Yii::t('app', 'Knockout'),
                      ])
                    : Html::tag('span', Html::encode(Yii::t('app', 'Time')), [
                        'class' => 'label label-warning auto-tooltip',
                        'title' => Yii::t('app', 'Time is up'),
                      ])
                  )
                  : ''
              ];
              return implode('&nbsp', array_filter($parts, function (string $value): bool {
                return trim((string)$value) !== '';
              }));
            },
            // }}}
          ],
          [
            // K/D {{{
            'label' => Yii::t('app', 'k') . '/' . Yii::t('app', 'd'),
            'headerOptions' => ['class' => 'cell-kd'],
            'contentOptions' => ['class' => 'cell-kd nobr'],
            'format' => 'raw',
            'value' => function ($model): string {
              return implode(' ', [
                Html::tag(
                  'span', 
                  $model->kill === null
                    ? Html::encode('?')
                    : ($model->death !== null && $model->kill >= $model->death
                      ? Html::tag('strong', Html::encode($model->kill))
                      : Html::encode($model->kill)
                    ),
                  ['class' => 'kill']
                ),
                '/',
                Html::tag(
                  'span', 
                  $model->death === null
                    ? Html::encode('?')
                    : ($model->kill !== null && $model->kill <= $model->death
                      ? Html::tag('strong', Html::encode($model->death))
                      : Html::encode($model->death)
                    ),
                  ['class' => 'death']
                ),
                $model->kill !== null && $model->death !== null
                  ? (
                    (function (int $k, int $d) {
                      if ($k > $d) {
                        return Html::tag('span', Html::encode('>'), ['class' => 'label label-success']);
                      } elseif ($k < $d) {
                        return Html::tag('span', Html::encode('<'), ['class' => 'label label-danger']);
                      } else {
                        return Html::tag('span', Html::encode('='), ['class' => 'label label-default']);
                      }
                    })($model->kill, $model->death)
                  )
                  : '',
              ]);
            },
            // }}}
          ],
          [
            // kills/min {{{
            'label' => Yii::t('app', 'K/min'),
            'headerOptions' => ['class' => 'cell-kill-min'],
            'contentOptions' => ['class' => 'cell-kill-min text-right'],
            'format' => 'raw',
            'value' => function ($model): ?string {
              $kill = $model->kill ?? null;
              $time = $model->elapsedTime ?? null;
              if ($kill === null || $time === null || $time < 1) {
                return null;
              }
              $value = Yii::$app->formatter->asDecimal($kill * 60 / $time, 3);
              return ($model->death ?? 9999) <= $kill
                ? Html::tag('strong', Html::encode($value))
                : Html::encode($value);
            },
            // }}}
          ],
          [
            // deaths/min {{{
            'label' => Yii::t('app', 'D/min'),
            'headerOptions' => ['class' => 'cell-death-min'],
            'contentOptions' => ['class' => 'cell-death-min text-right'],
            'format' => 'raw',
            'value' => function ($model): ?string {
              $death = $model->death ?? null;
              $time = $model->elapsedTime ?? null;
              if ($death === null || $time === null || $time < 1) {
                return null;
              }
              $value = Yii::$app->formatter->asDecimal($death * 60 / $time, 3);
              return ($model->kill ?? 9999) <= $death
                ? Html::tag('strong', Html::encode($value))
                : Html::encode($value);
            },
            // }}}
          ],
          [
            // kill ratio {{{
            'class' => KillRatioColumn::class,
            'killRate' => false,
            // }}}
          ],
          [
            // kill rate {{{
            'class' => KillRatioColumn::class,
            'killRate' => true,
            // }}}
          ],
          [
            // kill or assist {{{
            'label' => Yii::t('app', 'Kill or Assist'),
            'attribute' => 'kill_or_assist',
            'headerOptions' => ['class' => 'cell-kill-or-assist'],
            'contentOptions' => ['class' => 'cell-kill-or-assist'],
            'format' => 'integer',
            // }}}
          ],
          [
            // specials {{{
            'label' => Yii::t('app', 'Specials'),
            'attribute' => 'special',
            'headerOptions' => ['class' => 'cell-specials'],
            'contentOptions' => ['class' => 'cell-specials text-right'],
            'format' => 'integer',
            // }}}
          ],
          [
            // specials/min {{{
            'label' => Yii::t('app', 'S/min'),
            'headerOptions' => ['class' => 'cell-specials-min'],
            'contentOptions' => ['class' => 'cell-specials-min text-right'],
            'format' => ['decimal', 3],
            'value' => function ($model): ?float {
              $specials = $model->special ?? null;
              $time = $model->elapsedTime ?? null;
              return ($specials === null || $time === null || $time < 1)
                ? null
                : ($specials * 60 / $time);
            },
            // }}}
          ],
          [
            // inked {{{
            'label' => Yii::t('app', 'Inked'),
            'attribute' => 'inked',
            'headerOptions' => ['class' => 'cell-point'],
            'contentOptions' => ['class' => 'cell-point text-right'],
            'format' => 'integer',
            // }}}
          ],
          [
            // inked/min {{{
            'label' => Yii::t('app', 'Inked/min'),
            'headerOptions' => ['class' => 'cell-inked-min'],
            'contentOptions' => ['class' => 'cell-inked-min text-right'],
            'format' => ['decimal', 1],
            'value' => function ($model): ?float {
              $inked = $model->inked ?? null;
              $time = $model->elapsedTime ?? null;
              return ($inked === null || $time === null || $time < 1)
                ? null
                : ($inked * 60 / $time);
            },
            // }}}
          ],
          [
            // rank in team {{{
            'label' => Yii::t('app', 'Rank in Team'),
            'attribute' => 'rank_in_team',
            'headerOptions' => ['class' => 'cell-rank-in-team'],
            'contentOptions' => ['class' => 'cell-rank-in-team'],
            'format' => 'integer',
            // }}}
          ],
          [
            // elapsed (mm:ss) {{{
            'label' => Yii::t('app', 'Elapsed'),
            'headerOptions' => ['class' => 'cell-elapsed'],
            'contentOptions' => ['class' => 'cell-elapsed text-right'],
            'value' => function (Battle2 $model): string {
              if (!$value = $model->elapsedTime) {
                  return '';
              }
              return vsprintf('%d:%02d', [
                (int)floor($value / 60),
                ($value % 60),
              ]);
            },
            // }}}
          ],
          [
            // elapsed (sec) {{{
            'label' => Yii::t('app', 'Elapsed'),
            'headerOptions' => ['class' => 'cell-elapsed-sec'],
            'contentOptions' => ['class' => 'cell-elapsed-sec text-right'],
            'format' => 'integer',
            'attribute' => 'elapsedTime',
            // }}}
          ],
          [
            // datetime {{{
            'label' => Yii::t('app', 'Date Time'),
            'attribute' => 'end_at',
            'headerOptions' => ['class' => 'cell-datetime'],
            'contentOptions' => ['class' => 'cell-datetime'],
            'format' => ['htmlDatetime', 'short'],
            // }}}
          ],
          [
            // timezone {{{
            'label' => Yii::t('app', 'TZ'),
            'attribute' => 'end_at',
            'headerOptions' => ['class' => 'cell-datetime-timezone'],
            'contentOptions' => ['class' => 'cell-datetime-timezone'],
            'format' => ['datetime', 'zzz'],
            // }}}
          ],
          [
            // reltime {{{
            'label' => Yii::t('app', 'Relative Time'),
            'attribute' => 'end_at',
            'headerOptions' => ['class' => 'cell-reltime'],
            'contentOptions' => ['class' => 'cell-reltime'],
            'format' => 'htmlRelative',
            // }}}
          ],
        ], // }}}
      ]) . "\n" ?>
      <div class="text-center">
        <?= ListView::widget([
          'dataProvider' => $battleDataProvider,
          'itemOptions' => [ 'tag' => false ],
          'layout' => '{pager}',
          'pager' => [
            'maxButtonCount' => 5
          ]
        ]) . "\n" ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= Battle2FilterWidget::widget([
        'route' => 'show-v2/user',
        'screen_name' => $user->screen_name,
        'connectivity' => true,
        'filter' => $filter,
        'filterText' => $filter->filter != '',
        'withTeam' => true,
      ]) . "\n" ?>
      <?= UserMiniInfo2::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12" id="table-config">
      <div>
        <label>
          <input type="checkbox" id="table-hscroll" value="1">
          <?= Html::encode(Yii::t('app', 'Always enable horizontal scroll')) . "\n" ?>
        <label>
      </div>
      <div class="row"><?php
        $_list = [
          'cell-splatnet'             => Yii::t('app', 'SplatNet Battle #'),
          'cell-lobby-icon'           => Yii::t('app', 'Lobby (Icon)'),
          'cell-lobby'                => Yii::t('app', 'Lobby'),
          'cell-room'                 => Yii::t('app', 'Room info (Private)'),
          'cell-rule-icon'            => Yii::t('app', 'Mode (Icon)'),
          'cell-rule'                 => Yii::t('app', 'Mode'),
          'cell-rule-short'           => Yii::t('app', 'Mode (Short)'),
          'cell-special-battle'       => Yii::t('app', 'Special Battle (Fest)'),
          'cell-map'                  => Yii::t('app', 'Stage'),
          'cell-map-short'            => Yii::t('app', 'Stage (Short)'),
          'cell-main-weapon-icon'     => Yii::t('app', 'Weapon (Icon)'),
          'cell-main-weapon'          => Yii::t('app', 'Weapon'),
          'cell-main-weapon-short'    => Yii::t('app', 'Weapon (Short)'),
          'cell-freshness'            => Yii::t('app', 'Freshness'),
          'cell-sub-weapon-icon'      => Yii::t('app', 'Sub Weapon (Icon)'),
          'cell-sub-weapon'           => Yii::t('app', 'Sub Weapon'),
          'cell-special-icon'         => Yii::t('app', 'Special (Icon)'),
          'cell-special'              => Yii::t('app', 'Special'),
          'cell-team-icon'            => Yii::t('app', 'Team Icon'),
          'cell-team-id'              => Yii::t('app', 'Team ID'),
          'cell-rank'                 => Yii::t('app', 'Rank'),
          'cell-rank-after'           => Yii::t('app', 'Rank (After)'),
          'cell-x-power'              => Yii::t('app', 'X Power'),
          'cell-gachi-power'          => Yii::t('app', 'Power Level'),
          'cell-league-power'         => Yii::t('app', 'League Power'),
          'cell-fest-power'           => Yii::t('app', 'Splatfest Power'),
          'cell-fest-title'           => Yii::t('app', 'Splatfest Title'),
          'cell-fest-title-after'     => Yii::t('app', 'Splatfest Title (After)'),
          'cell-level'                => Yii::t('app', 'Level'),
          'cell-judge'                => Yii::t('app', 'Judge'),
          'cell-result'               => Yii::t('app', 'Result'),
          'cell-kd'                   => Yii::t('app', 'k') . '/' . Yii::t('app', 'd'),
          'cell-kill-min'             => Yii::t('app', 'Kills/min'),
          'cell-death-min'            => Yii::t('app', 'Deaths/min'),
          'cell-kill-ratio'           => Yii::t('app', 'Kill Ratio'),
          'cell-kill-rate'            => Yii::t('app', 'Kill Rate'),
          'cell-kill-or-assist'       => Yii::t('app', 'Kill or Assist'),
          'cell-specials'             => Yii::t('app', 'Specials'),
          'cell-specials-min'         => Yii::t('app', 'Specials/min'),
          'cell-point'                => Yii::t('app', 'Turf Inked'),
          'cell-inked-min'            => Yii::t('app', 'Inked/min'),
          'cell-rank-in-team'         => Yii::t('app', 'Rank in Team'),
          'cell-elapsed'              => Yii::t('app', 'Elapsed Time'),
          'cell-elapsed-sec'          => Yii::t('app', 'Elapsed Time (seconds)'),
          'cell-datetime'             => Yii::t('app', 'Date Time'),
          'cell-datetime-timezone'    => Yii::t('app', 'Time Zone'),
          'cell-reltime'              => Yii::t('app', 'Relative Time'),
        ];
        foreach ($_list as $k => $v) {
          echo Html::tag(
            'div',
            Html::tag(
              'label',
              sprintf(
                '%s %s',
                Html::tag('input', '', ['type' => 'checkbox', 'class' => 'table-config-chk', 'data-klass' => $k]),
                Html::encode($v)
              )
            ),
            ['class' => 'col-xs-6 col-sm-4 col-lg-3']
          );
        }
      ?></div>
    </div>
  </div>
</div>
