<?php
use app\components\widgets\AdWidget;
use app\components\widgets\Battle2FilterWidget;
use app\components\widgets\EmbedVideo;
use app\components\widgets\SnsWidget;
use rmrevin\yii\fontawesome\FontAwesome;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

$title = Yii::t('app', "{0}'s Splat Log", $user->name);
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
          $summary->kd_present > 0 ? $fmt->asDecimal($summary->total_kill / $summary->kd_present, 2) : '-',
          $summary->kd_present > 0 ? $fmt->asDecimal($summary->total_death / $summary->kd_present, 2) : '-',
          $summary->kd_present > 0
            ? ($summary->total_death == 0
              ? ($summary->total_kill == 0 ? '-' : '∞')
              : $fmt->asDecimal($summary->total_kill / $summary->total_death, 2)
            )
            : '-',
        ])
      );
    })(),
    'feedUrl' => Url::to(['feed/user-v2', 'screen_name' => $user->screen_name, 'type' => 'rss', 'lang' => Yii::$app->language], true),
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <div class="text-right">
        <?= ListView::widget([
          'dataProvider' => $battleDataProvider,
          'itemOptions' => [ 'tag' => false ],
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
          <span class="fa fa-search fa-fw"></span>
          <?= Html::encode(Yii::t('app', 'Search')) . "\n" ?>
        </a>
        <a href="#table-config" class="btn btn-default">
          <span class="fa fa-cogs fa-fw"></span>
          <?= Html::encode(Yii::t('app', 'View Settings')) . "\n" ?>
        </a>
        <?= Html::a(
          '<span class="fa fa-list fa-fw"></span> ' . Html::encode(Yii::t('app', 'Simplified List')),
          array_merge($filter->toQueryParams(), ['show-v2/user', 'screen_name' => $user->screen_name, 'v' => 'simple']),
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
        'tableOptions' => ['class' => 'table table-striped table-condensed'],
        'rowOptions' => function ($model) : array {
          return [
            'class' => [
              'battle-row',
              $model->getHasDisconnectedPlayer() ? 'disconnected' : '',
            ],
            'data' => [
              'period' => $model->period,
            ],
          ];
        },
        'columns' => [ // {{{
          [
            // button {{{
            'format' => 'raw',
            'value' => function ($model) : string {
              return trim(implode(' ', [
                Html::a(
                  Yii::t('app', 'Detail'),
                  ['show-v2/battle', 'screen_name' => $model->user->screen_name, 'battle' => $model->id],
                  ['class' => 'btn btn-primary btn-xs']
                ),
                (!$model->link_url) ? '' : Html::a(
                  FontAwesome::icon(EmbedVideo::isSupported($model->link_url) ? 'video-camera' : 'link')->fixedWidth(),
                  $model->link_url,
                  ['class' => 'btn btn-default btn-xs', 'rel' => 'nofollow']
                ),
              ]));
            },
            'contentOptions' => ['class' => 'nobr'],
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
                      return Yii::t('app-rule2', 'Splatfest (Solo)');

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
            // mode {{{
            'label' => Yii::t('app', 'Mode'),
            'headerOptions' => ['class' => 'cell-rule'],
            'contentOptions' => ['class' => 'cell-rule'],
            'value' => function ($model) : string {
              return Yii::t('app-rule2', $model->rule->name ?? '?');
            },
            // }}}
          ],
          [
            // mode (short) {{{
            'label' => Yii::t('app', 'Mode'),
            'headerOptions' => ['class' => 'cell-rule-short'],
            'contentOptions' => ['class' => 'cell-rule-short'],
            'format' => 'raw',
            'value' => function ($model) : string {
              return Html::tag(
                'span',
                Html::encode(Yii::t('app-rule2', $model->rule->short_name ?? '?')),
                ['class' => 'auto-tooltip', 'title' => $model->rule->name ?? '?']
              );
            },
            // }}}
          ],
          [
            // stage {{{
            'label' => Yii::t('app', 'Stage'),
            'headerOptions' => ['class' => 'cell-map'],
            'contentOptions' => ['class' => 'cell-map'],
            'value' => function ($model) : string {
              return Yii::t('app-map2', $model->map->name ?? '?');
            },
            // }}}
          ],
          [
            // stage (short) {{{
            'label' => Yii::t('app', 'Stage'),
            'headerOptions' => ['class' => 'cell-map-short'],
            'contentOptions' => ['class' => 'cell-map-short'],
            'value' => function ($model) : string {
              return Yii::t('app-map2', $model->map->short_name ?? '?');
            },
            // }}}
          ],
          [
            // weapon {{{
            'label' => Yii::t('app', 'Weapon'),
            'headerOptions' => ['class' => 'cell-main-weapon'],
            'contentOptions' => ['class' => 'cell-main-weapon'],
            'format' => 'raw',
            'value' => function ($model) : string {
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
            'value' => function ($model) : string {
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
                Html::encode(
                  Yii::$app->weaponShortener->get(Yii::t('app-weapon2', $model->weapon->name ?? '?'))
                ),
                ['class' => 'auto-tooltip', 'title' => $title]
              );
            },
            // }}}
          ],
          [
            // sub weapon {{{
            'label' => Yii::t('app', 'Sub Weapon'),
            'headerOptions' => ['class' => 'cell-sub-weapon'],
            'contentOptions' => ['class' => 'cell-sub-weapon'],
            'value' => function ($model) : string {
              return Yii::t('app-subweapon2', $model->weapon->subweapon->name ?? '?');
            },
            // }}} 
          ],
          [
            // special weapon {{{
            'label' => Yii::t('app', 'Special'),
            'headerOptions' => ['class' => 'cell-special'],
            'contentOptions' => ['class' => 'cell-special'],
            'value' => function ($model) : string {
              return Yii::t('app-special2', $model->weapon->special->name ?? '?');
            },
            // }}}
          ],
          [
            // team id (icon) {{{
            'label' => Yii::t('app', 'Team'),
            'headerOptions' => ['class' => 'cell-team-icon'],
            'contentOptions' => ['class' => 'cell-team-icon text-center'],
            'format' => 'raw',
            'value' => function ($model) : string {
              return trim($model->my_team_id) === ''
                ? ''
                : Html::img(
                  $model->myTeamIcon,
                  [
                    'title' => $model->my_team_id,
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
            // team id {{{
            'label' => Yii::t('app', 'Team ID'),
            'headerOptions' => ['class' => 'cell-team-id'],
            'contentOptions' => ['class' => 'cell-team-id'],
            'format' => 'raw',
            'value' => function ($model) : string {
              return trim($model->my_team_id) === ''
                ? ''
                : Html::tag('code', Html::encode(trim($model->my_team_id)));
            },
            // }}}
          ],
          [
            // rank {{{
            'label' => Yii::t('app', 'Rank'),
            'headerOptions' => ['class' => 'cell-rank'],
            'contentOptions' => ['class' => 'cell-rank'],
            'value' => function ($model) : string {
              if (!$rank = $model->rank) {
                return '';
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
            // rank (after) {{{
            'label' => Yii::t('app', 'Rank (After)'),
            'headerOptions' => ['class' => 'cell-rank-after'],
            'contentOptions' => ['class' => 'cell-rank-after'],
            'value' => function ($model) : string {
              if (!$rank = $model->rankAfter) {
                return '';
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
            // gachi power {{{
            'label' => Yii::t('app', 'Power Level'),
            'headerOptions' => ['class' => 'cell-gachi-power'],
            'contentOptions' => ['class' => 'cell-gachi-power text-right'],
            'value' => function ($model) : string {
              if ($model->estimate_gachi_power < 1) {
                return '';
              }
              return $model->estimate_gachi_power;
            },
            // }}}
          ],
          [
            // league power {{{
            'label' => Yii::t('app', 'League Power'),
            'headerOptions' => ['class' => 'cell-league-power'],
            'contentOptions' => ['class' => 'cell-league-power text-right'],
            'value' => function ($model) : string {
              if ($model->league_point < 1) {
                return '';
              }
              return $model->league_point;
            },
            // }}}
          ],
          [
            // level {{{
            'label' => Yii::t('app', 'Level'),
            'headerOptions' => ['class' => 'cell-level'],
            'contentOptions' => ['class' => 'cell-level'],
            'value' => function ($model) : string {
              return $model->level ?? '';
            },
            // }}}
          ],
          [
            // judge {{{
            'label' => Yii::t('app', 'Judge'),
            'headerOptions' => ['class' => 'cell-judge'],
            'contentOptions' => ['class' => 'cell-judge'],
            'format' => 'raw',
            'value' => function ($model) : string {
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
            'value' => function ($model) : string {
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
              return implode('&nbsp', array_filter($parts, function (string $value) : bool {
                return trim($value) != '';
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
            'value' => function ($model) : string {
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
            'value' => function ($model) : string {
              $kill = $model->kill ?? null;
              $time = $model->elapsedTime ?? null;
              if ($kill === null || $time === null || $time < 1) {
                return '';
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
            'value' => function ($model) : string {
              $death = $model->death ?? null;
              $time = $model->elapsedTime ?? null;
              if ($death === null || $time === null || $time < 1) {
                return '';
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
            'label' => Yii::t('app', 'Ratio'),
            'headerOptions' => ['class' => 'cell-kill-ratio auto-tooltip', 'title' => Yii::t('app', 'Kill Ratio')],
            'contentOptions' => function ($model) : array {
              return $model->kill_ratio === null
                ? [
                  'class' => [
                    'cell-kill-ratio',
                  ],
                ]
                : [
                  'class' => [
                    'cell-kill-ratio',
                    'kill-ratio',
                    'text-right',
                  ],
                  'data' => [
                    'kill-ratio' => $model->kill_ratio,
                  ],
                ];
            },
            'value' => function ($model) : string {
              return ($model->kill_ratio !== null)
                ? Yii::$app->formatter->asDecimal($model->kill_ratio, 2)
                : '';
            },
            // }}}
          ],
          [
            // kill rate {{{
            'label' => Yii::t('app', 'Rate'),
            'headerOptions' => ['class' => 'cell-kill-rate auto-tooltip', 'title' => Yii::t('app', 'Kill Rate')],
            'contentOptions' => function ($model) : array {
              return $model->kill_ratio === null
                ? [
                  'class' => [
                    'cell-kill-rate',
                  ],
                ]
                : [
                  'class' => [
                    'cell-kill-rate',
                    'kill-rate',
                    'text-right',
                  ],
                  'data' => [
                    'kill-ratio' => $model->kill_ratio,
                  ],
                ];
            },
            'value' => function ($model) : string {
              return ($model->kill_rate !== null)
                ? Yii::$app->formatter->asPercent($model->kill_rate / 100, 2)
                : '';
            },
            // }}}
          ],
          [
            // kill or assist {{{
            'label' => Yii::t('app', 'Kill or Assist'),
            'headerOptions' => ['class' => 'cell-kill-or-assist'],
            'contentOptions' => ['class' => 'cell-kill-or-assist'],
            'value' => function ($model) : string {
              return $model->kill_or_assist ?? '';
            },
            // }}}
          ],
          [
            // specials {{{
            'label' => Yii::t('app', 'Specials'),
            'headerOptions' => ['class' => 'cell-specials'],
            'contentOptions' => ['class' => 'cell-specials text-right'],
            'value' => function ($model) : string {
              return $model->special ?? '';
            },
            // }}}
          ],
          [
            // specials/min {{{
            'label' => Yii::t('app', 'S/min'),
            'headerOptions' => ['class' => 'cell-specials-min'],
            'contentOptions' => ['class' => 'cell-specials-min text-right'],
            'value' => function ($model) : string {
              $specials = $model->special ?? null;
              $time = $model->elapsedTime ?? null;
              return ($specials === null || $time === null || $time < 1)
                ? ''
                : Yii::$app->formatter->asDecimal($specials * 60 / $time, 3);
            },
            // }}}
          ],
          [
            // inked {{{
            'label' => Yii::t('app', 'Inked'),
            'headerOptions' => ['class' => 'cell-point'],
            'contentOptions' => ['class' => 'cell-point text-right'],
            'value' => function ($model) : string {
              $value = $model->inked ?? null;
              return ($value === null)
                ? ''
                : Yii::$app->formatter->asInteger($value);
            },
            // }}}
          ],
          [
            // inked/min {{{
            'label' => Yii::t('app', 'Inked/min'),
            'headerOptions' => ['class' => 'cell-inked-min'],
            'contentOptions' => ['class' => 'cell-inked-min text-right'],
            'value' => function ($model) : string {
              $inked = $model->inked ?? null;
              $time = $model->elapsedTime ?? null;
              return ($inked === null || $time === null || $time < 1)
                ? ''
                : Yii::$app->formatter->asDecimal($inked * 60 / $time, 1);
            },
            // }}}
          ],
          [
            // rank in team {{{
            'label' => Yii::t('app', 'Rank in Team'),
            'headerOptions' => ['class' => 'cell-rank-in-team'],
            'contentOptions' => ['class' => 'cell-rank-in-team'],
            'value' => function ($model) : string {
              return $model->rank_in_team ?? '';
            },
            // }}}
          ],
          [
            // datetime {{{
            'label' => Yii::t('app', 'Date Time'),
            'headerOptions' => ['class' => 'cell-datetime'],
            'contentOptions' => ['class' => 'cell-datetime'],
            'format' => 'raw',
            'value' => function ($model) : string {
              return $model->end_at === null
                ? Html::encode(Yii::t('app', 'N/A'))
                : Html::tag(
                  'time',
                  Html::encode(Yii::$app->formatter->asDateTime($model->end_at, 'short')),
                  ['datetime' => Yii::$app->formatter->asDateTime($model->end_at, 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ')]
                );
            },
            // }}}
          ],
          [
            // reltime {{{
            'label' => Yii::t('app', 'Relative Time'),
            'headerOptions' => ['class' => 'cell-reltime'],
            'contentOptions' => ['class' => 'cell-reltime'],
            'format' => 'raw',
            'value' => function ($model) : string {
              return $model->end_at === null
                ? Html::encode(Yii::t('app', 'N/A'))
                : Html::tag(
                  'time',
                  Html::encode(Yii::$app->formatter->asRelativeTime($model->end_at)),
                  ['datetime' => Yii::$app->formatter->asDateTime($model->end_at, 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ')]
                );
            },
            // }}}
          ],
        ], // }}}
      ]) . "\n" ?>
      <div class="text-right">
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
        'filter' => $filter,
      ]) . "\n" ?>
      <?= $this->render('/includes/user-miniinfo2', ['user' => $user]) . "\n" ?>
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
          'cell-lobby'                => Yii::t('app', 'Lobby'),
          'cell-rule'                 => Yii::t('app', 'Mode'),
          'cell-rule-short'           => Yii::t('app', 'Mode (Short)'),
          'cell-map'                  => Yii::t('app', 'Stage'),
          'cell-map-short'            => Yii::t('app', 'Stage (Short)'),
          'cell-main-weapon'          => Yii::t('app', 'Weapon'),
          'cell-main-weapon-short'    => Yii::t('app', 'Weapon (Short)'),
          'cell-sub-weapon'           => Yii::t('app', 'Sub Weapon'),
          'cell-special'              => Yii::t('app', 'Special'),
          'cell-team-icon'            => Yii::t('app', 'Team Icon'),
          'cell-team-id'              => Yii::t('app', 'Team ID'),
          'cell-rank'                 => Yii::t('app', 'Rank'),
          'cell-rank-after'           => Yii::t('app', 'Rank (After)'),
          'cell-gachi-power'          => Yii::t('app', 'Power Level'),
          'cell-league-power'         => Yii::t('app', 'League Power'),
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
          'cell-datetime'             => Yii::t('app', 'Date Time'),
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
<?php
$this->registerJs('window.battleList();window.battleListConfig();');
?>
