<?php

declare(strict_types=1);

use app\assets\BattleListAsset;
use app\assets\BattleListGroupHeaderAsset;
use app\assets\Spl2WeaponAsset;
use app\components\grid\KillRatioColumn;
use app\components\helpers\Battle as BattleHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\EmbedVideo;
use app\components\widgets\FA;
use app\components\widgets\GameModeIcon;
use app\components\widgets\Label;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\components\widgets\v3\Result;
use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\models\Battle3;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

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
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
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
      <?= $this->render('//includes/battles-summary', [
        'headingText' => Yii::t('app', 'Summary: Based on the current filter'),
        'summary' => $summary
      ]) . "\n" ?>
      <div style="margin-bottom:10px">
        <a href="#table-config" class="btn btn-default">
          <?= FA::fas('cogs')->fw() . "\n" ?>
          <?= Html::encode(Yii::t('app', 'View Settings')) . "\n" ?>
        </a>
        <?= Html::a(
          implode(' ', [
            (string)FA::fas('list')->fw(),
            Html::encode(Yii::t('app', 'Simplified List')),
          ]),
          ['show-v3/user', 'screen_name' => $user->screen_name, 'v' => 'simple'],
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
        'rowOptions' => function (Battle3 $model): array {
          return [
            'class' => [
              'battle-row',
              // $model->getHasDisconnectedPlayer() ? 'disconnected' : '',
            ],
          ];
        },
        'beforeRow' => function (Battle3 $model, int $key, int $index, GridView $widget): ?string {
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
        'columns' => [
          [
            // button {{{
            'format' => 'raw',
            'value' => function ($model): string {
              return trim(implode(' ', [
                Html::a(
                  Yii::t('app', 'Detail'),
                  ['show-v3/battle', 'screen_name' => $model->user->screen_name, 'battle' => $model->uuid],
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
            // lobby {{{
            'label' => Yii::t('app', 'Lobby'),
            'attribute' => 'lobby.name',
            'headerOptions' => ['class' => 'cell-lobby'],
            'contentOptions' => ['class' => 'cell-lobby'],
            'format' => ['translated', 'app-lobby3'],
            // }}}
          ],
          [
            // mode {{{
            'label' => Yii::t('app', 'Mode'),
            'attribute' => 'rule.name',
            'headerOptions' => ['class' => 'cell-rule'],
            'contentOptions' => ['class' => 'cell-rule'],
            'format' => ['translated', 'app-rule3'],
            // }}}
          ],
          [
            // stage {{{
            'label' => Yii::t('app', 'Stage'),
            'attribute' => 'map.name',
            'headerOptions' => ['class' => 'cell-map'],
            'contentOptions' => ['class' => 'cell-map'],
            'format' => ['translated', 'app-map3'],
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
                  Yii::t('app-subweapon3', $model->weapon->subweapon->name ?? '?'),
                ]),
                implode(' ', [
                  Yii::t('app', 'Special:'),
                  Yii::t('app-special3', $model->weapon->special->name ?? '?'),
                ]),
              ]);
              return Html::tag(
                'span',
                Html::encode(Yii::t('app-weapon3', $model->weapon->name ?? '?')),
                ['class' => 'auto-tooltip', 'title' => $title]
              );
            },
            // }}}
          ],
          [
            // sub weapon (icon) {{{
            'label' => '',
            'headerOptions' => ['class' => 'cell-sub-weapon-icon'],
            'contentOptions' => ['class' => 'cell-sub-weapon-icon'],
            'format' => 'raw',
            'value' => function (Battle3 $model): string {
              if ($w = $model->weapon) {
                if ($sub = $w->subweapon) {
                  return SubweaponIcon::widget(['model' => $sub]);
                }
              }
              return '?';
            },
            // }}}
          ],
          [
            // sub weapon {{{
            'label' => Yii::t('app', 'Sub Weapon'),
            'attribute' => 'weapon.subweapon.name',
            'headerOptions' => ['class' => 'cell-sub-weapon'],
            'contentOptions' => ['class' => 'cell-sub-weapon'],
            'format' => ['translated', 'app-subweapon3'],
            // }}} 
          ],
          [
            // special weapon (icon) {{{
            'label' => '',
            'headerOptions' => ['class' => 'cell-special-icon'],
            'contentOptions' => ['class' => 'cell-special-icon'],
            'format' => 'raw',
            'value' => function (Battle3 $model): string {
              if ($w = $model->weapon) {
                if ($special = $w->special) {
                  return SpecialIcon::widget(['model' => $special]);
                }
              }
              return '?';
            },
            // }}}
          ],
          [
            // special weapon {{{
            'label' => Yii::t('app', 'Special'),
            'attribute' => 'weapon.special.name',
            'headerOptions' => ['class' => 'cell-special'],
            'contentOptions' => ['class' => 'cell-special'],
            'format' => ['translated', 'app-special3'],
            // }}}
          ],
          [
            // rank (before) {{{
            'label' => Yii::t('app', 'Rank'),
            'headerOptions' => ['class' => 'cell-rank'],
            'contentOptions' => ['class' => 'cell-rank'],
            'value' => function ($model): ?string {
              if (!$rank = $model->rankBefore) {
                return null;
              }
              if ($rank->key === 's+' && $model->rank_before_s_plus !== null) {
                return sprintf(
                  '%s %d',
                  Yii::t('app-rank3', $rank->name),
                  $model->rank_before_s_plus
                );
              }
              return Yii::t('app-rank3', $rank->name);
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
              if ($rank->key === 's+' && $model->rank_after_s_plus !== null) {
                return sprintf(
                  '%s %d',
                  Yii::t('app-rank3', $rank->name),
                  $model->rank_after_s_plus
                );
              }
              return Yii::t('app-rank3', $rank->name);
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
            'value' => function (Battle3 $model): string {
              $result = Result::widget([
                'isKnockout' => $model->is_knockout,
                'result' => $model->result,
                'rule' => $model->rule,
                'separator' => mb_chr(0xa0, 'UTF-8'),
              ]);

              return $result ?: Html::encode('?');
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
          // [
          //   // kills/min {{{
          //   'label' => Yii::t('app', 'K/min'),
          //   'headerOptions' => ['class' => 'cell-kill-min'],
          //   'contentOptions' => ['class' => 'cell-kill-min text-right'],
          //   'format' => 'raw',
          //   'value' => function ($model): ?string {
          //     $kill = $model->kill ?? null;
          //     $time = $model->elapsedTime ?? null;
          //     if ($kill === null || $time === null || $time < 1) {
          //       return null;
          //     }
          //     $value = Yii::$app->formatter->asDecimal($kill * 60 / $time, 3);
          //     return ($model->death ?? 9999) <= $kill
          //       ? Html::tag('strong', Html::encode($value))
          //       : Html::encode($value);
          //   },
          //   // }}}
          // ],
          // [
          //   // deaths/min {{{
          //   'label' => Yii::t('app', 'D/min'),
          //   'headerOptions' => ['class' => 'cell-death-min'],
          //   'contentOptions' => ['class' => 'cell-death-min text-right'],
          //   'format' => 'raw',
          //   'value' => function ($model): ?string {
          //     $death = $model->death ?? null;
          //     $time = $model->elapsedTime ?? null;
          //     if ($death === null || $time === null || $time < 1) {
          //       return null;
          //     }
          //     $value = Yii::$app->formatter->asDecimal($death * 60 / $time, 3);
          //     return ($model->kill ?? 9999) <= $death
          //       ? Html::tag('strong', Html::encode($value))
          //       : Html::encode($value);
          //   },
          //   // }}}
          // ],
          // [
          //   // kill ratio {{{
          //   'class' => KillRatioColumn::class,
          //   'killRate' => false,
          //   // }}}
          // ],
          // [
          //   // kill rate {{{
          //   'class' => KillRatioColumn::class,
          //   'killRate' => true,
          //   // }}}
          // ],
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
          // [
          //   // specials/min {{{
          //   'label' => Yii::t('app', 'S/min'),
          //   'headerOptions' => ['class' => 'cell-specials-min'],
          //   'contentOptions' => ['class' => 'cell-specials-min text-right'],
          //   'format' => ['decimal', 3],
          //   'value' => function ($model): ?float {
          //     $specials = $model->special ?? null;
          //     $time = $model->elapsedTime ?? null;
          //     return ($specials === null || $time === null || $time < 1)
          //       ? null
          //       : ($specials * 60 / $time);
          //   },
          //   // }}}
          // ],
          [
            // inked {{{
            'label' => Yii::t('app', 'Inked'),
            'attribute' => 'inked',
            'headerOptions' => ['class' => 'cell-point'],
            'contentOptions' => ['class' => 'cell-point text-right'],
            'format' => 'integer',
            // }}}
          ],
          // [
          //   // inked/min {{{
          //   'label' => Yii::t('app', 'Inked/min'),
          //   'headerOptions' => ['class' => 'cell-inked-min'],
          //   'contentOptions' => ['class' => 'cell-inked-min text-right'],
          //   'format' => ['decimal', 1],
          //   'value' => function ($model): ?float {
          //     $inked = $model->inked ?? null;
          //     $time = $model->elapsedTime ?? null;
          //     return ($inked === null || $time === null || $time < 1)
          //       ? null
          //       : ($inked * 60 / $time);
          //   },
          //   // }}}
          // ],
          [
            // rank in team {{{
            'label' => Yii::t('app', 'Rank in Team'),
            'attribute' => 'rank_in_team',
            'headerOptions' => ['class' => 'cell-rank-in-team'],
            'contentOptions' => ['class' => 'cell-rank-in-team'],
            'format' => 'integer',
            // }}}
          ],
          // [
          //   // elapsed (mm:ss) {{{
          //   'label' => Yii::t('app', 'Elapsed'),
          //   'headerOptions' => ['class' => 'cell-elapsed'],
          //   'contentOptions' => ['class' => 'cell-elapsed text-right'],
          //   'value' => function (Battle3 $model): string {
          //     if (!$value = $model->elapsedTime) {
          //         return '';
          //     }
          //     return vsprintf('%d:%02d', [
          //       (int)floor($value / 60),
          //       ($value % 60),
          //     ]);
          //   },
          //   // }}}
          // ],
          // [
          //   // elapsed (sec) {{{
          //   'label' => Yii::t('app', 'Elapsed'),
          //   'headerOptions' => ['class' => 'cell-elapsed-sec'],
          //   'contentOptions' => ['class' => 'cell-elapsed-sec text-right'],
          //   'format' => 'integer',
          //   'attribute' => 'elapsedTime',
          //   // }}}
          // ],
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
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
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
          'cell-map'                  => Yii::t('app', 'Stage'),
          'cell-main-weapon'          => Yii::t('app', 'Weapon'),
          'cell-sub-weapon-icon'      => Yii::t('app', 'Sub Weapon (Icon)'),
          'cell-sub-weapon'           => Yii::t('app', 'Sub Weapon'),
          'cell-special-icon'         => Yii::t('app', 'Special (Icon)'),
          'cell-special'              => Yii::t('app', 'Special'),
          'cell-rank'                 => Yii::t('app', 'Rank'),
          'cell-rank-after'           => Yii::t('app', 'Rank (After)'),
          'cell-level'                => Yii::t('app', 'Level'),
          'cell-judge'                => Yii::t('app', 'Judge'),
          'cell-result'               => Yii::t('app', 'Result'),
          'cell-kd'                   => Yii::t('app', 'k') . '/' . Yii::t('app', 'd'),
          'cell-kill-or-assist'       => Yii::t('app', 'Kill or Assist'),
          'cell-specials'             => Yii::t('app', 'Specials'),
          'cell-point'                => Yii::t('app', 'Turf Inked'),
          'cell-rank-in-team'         => Yii::t('app', 'Rank in Team'),
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
