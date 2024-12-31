<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\assets\BattleEditAsset;
use app\assets\FontAwesomeAsset;
use app\assets\PhotoSwipeSimplifyAsset;
use app\assets\Spl2WeaponAsset;
use app\components\helpers\Battle as BattleHelper;
use app\components\widgets\BattleDeathReasonsTable;
use app\components\widgets\BattleKillDeathColumn;
use app\components\widgets\FA;
use app\components\widgets\FestPowerHistory;
use app\components\widgets\FreshnessHistory;
use app\components\widgets\Label;
use app\components\widgets\LeaguePowerHistory;
use app\components\widgets\TimestampColumnWidget;
use app\components\widgets\XPowerHistory;
use app\models\Battle2;
use app\models\BattleDeathReason2;
use app\models\Rank2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var Battle2 $battle
 * @var View $this
 */

?>
<?= DetailView::widget([
  'model' => $battle,
  'id' => 'battle',
  'options' => [
    'class' => ['table', 'table-striped'],
  ],
  'template' => function ($attribute, $index, $widget) {
    // {{{
    if ($attribute['value'] === null) {
      return;
    }
    $captionOptions = Html::renderTagAttributes(
      ArrayHelper::getValue($attribute, 'captionOptions', [])
    );
    $contentOptions = Html::renderTagAttributes(
      ArrayHelper::getValue($attribute, 'contentOptions', [])
    );
    return strtr(
      '<tr><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>',
      [
        '{label}' => $attribute['label'],
        '{value}' => $widget->formatter->format($attribute['value'], $attribute['format']),
        '{captionOptions}' => $captionOptions,
        '{contentOptions}' =>  $contentOptions,
      ]
    );
    // }}}
  },
  'attributes' => [
    [
      'label' => Yii::t('app', 'SplatNet Battle #'),
      'value' => function ($model) : ?string {
        $value = trim((string)$model->splatnet_number);
        if ($value === '') {
          return null;
        }
        return Yii::$app->formatter->asInteger($value);
      },
    ],
    [
      'label' => Yii::t('app', 'Mode'),
      'value' => function ($model) {
        if ($text = $model->getPrettyMode()) {
          return $text;
        }
        return sprintf(
          '%s %s',
          Yii::t('app', '(incomplete)'),
          implode(' / ', [
            Yii::t('app-rule2', $model->lobby->name ?? '?'),
            Yii::t('app-rule2', $model->mode->name ?? '?'),
            Yii::t('app-rule2', $model->rule->name ?? '?'),
          ])
        );
      },
    ],
    [
      'attribute' => 'map_id', // {{{
      'value' => function ($model) {
        return Yii::t('app-map2', $model->map->name ?? '?');
      },
      // }}}
    ],
    [
      'attribute' => 'weapon_id', // {{{
      'format' => 'raw',
      'value' => function ($model): ?string {
        return $this->render('_battle_details_weapon_name', [
          'battle' => $model,
        ]);
      },
      // }}}
    ],
    [
      'attribute' => 'freshness_id', // {{{
      'format' => 'raw',
      'value' => function ($model): ?string {
        if ($model->freshness === null) {
            return null;
        }

        if (!$freshness = $model->freshnessModel) {
            return null;
        }

        $statusLine = implode(' ', [
          Html::tag('span', (string)FA::fas('flag')->fw(), [
            'class' => [
              'freshness-flag',
              'freshness-flag-' . $freshness->color,
            ],
          ]),
          Html::encode(Yii::$app->formatter->asDecimal($model->freshness, 1)),
          '/',
          Html::encode(Yii::t('app-freshness2', $freshness->name)),
        ]);

        $history = FreshnessHistory::widget(['current' => $model]);

        return implode('<br>', array_filter([
          $statusLine,
          $history,
        ]));
      },
      // }}}
    ],
    [
      'attribute' => 'rank_id', // {{{
      'value' => function ($model) : ?string {
        if ($model->rank_id === null && $model->rank_after_id === null) {
          return null;
        }

        $renderRank = function (?Rank2 $rank, ?int $rankExp, ?float $xPower) : string {
            // {{{
            switch ($rank->key ?? '') {
                case '':
                    return '?';

                case 's+':
                    if ($rankExp !== null) {
                        return implode(' ', [
                            Yii::t('app-rank2', $rank->name),
                            (string)(int)$rankExp,
                        ]);
                    }
                    break;

                case 'x':
                    if ($xPower !== null) {
                        return vsprintf('%s (%s)', [
                            Yii::t('app-rank2', $rank->name),
                            Yii::$app->formatter->asDecimal($xPower, 1),
                        ]);
                    }
                    break;
            }

            return Yii::t('app-rank2', $rank->name);
            // }}}
        };

        return implode(' ', [
            $renderRank($model->rank, $model->rank_exp, $model->x_power),
            '→',
            $renderRank($model->rankAfter, $model->rank_after_exp, $model->x_power_after),
        ]);
      },
      // }}}
    ],
    [
      'attribute' => 'level', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if ($model->level === null && $model->level_after === null) {
          return null;
        }
        return sprintf(
          '%3$s%1$s → %3$s%2$s',
          Html::encode($model->level ?? '?'),
          Html::encode($model->level_after ?? '?'),
          (($model->star_rank ?? 0) > 0)
            ? Html::tag('span', Html::encode('★'), [
              'style' => [
                'vertical-align' => 'super',
                'font-size' => '0.75em',
              ],
              'class' => 'auto-tooltip',
              'title' => (string)$model->star_rank,
            ])
            : ''
        );
      },
      // }}}
    ],
    [
      'attribute' => 'special_battle_id', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if (!$model->special_battle_id || !$model->specialBattle) {
          return null;
        }
        $sp = $model->specialBattle;
        return Label::widget([
          'content' => Yii::t('app', $model->specialBattle->name),
          'color' => 'primary',
        ]);
      },
      // }}}
    ],
    [
      'attribute' => 'fest_title_id', // {{{
      'value' => function ($model) : ?string {
        $title1 = $model->festTitle;
        $title2 = $model->festTitleAfter;
        if (!$title1 && !$title2) {
          return null;
        }
        $gender = $model->gender;
        $theme = $model->myTeamFestTheme;
        $format = function ($title, $exp) use ($gender, $theme) : string {
          if (!$title) {
            return '?';
          }
          $themeName = $theme->name ?? '***';
          $name = Yii::t('app-fest', $title->getName($gender), [$themeName, $themeName]);
          return ($title->key === 'king' || $exp === null)
            ? $name
            : "{$name} {$exp}";
        };
        return sprintf(
          '%s → %s',
          $format($title1, $model->fest_exp),
          $format($title2, $model->fest_exp_after)
        );
      },
      // }}}
    ],
    [
      'attribute' => 'clout', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if ($model->clout === null &&
            $model->total_clout === null &&
            $model->total_clout_after === null
        ) {
            return null;
        }

        $synergy = '';
        if ($model->synergy_bonus > 1.0) {
          $synergy = Label::widget([
            'content' => sprintf(
              '%s: ×%.1f',
              Yii::t('app', 'Synergy Bonus'),
              (float)$model->synergy_bonus
            ),
            'color' => 'warning',
          ]);
        }

        $text = null;
        if ($model->total_clout === null && $model->total_clout_after === null) {
          $text = sprintf('+%s', Yii::$app->formatter->asInteger($model->clout));
        } else {
          $int = function (?int $value): string {
            return ($value === null) ? '?' : Yii::$app->formatter->asInteger($value);
          };
          $text = sprintf('%s → %s', $int($model->total_clout), $int($model->total_clout_after));
        }

        return trim(implode(' ', [
          Html::encode($text),
          $synergy,
        ]));
      },
      // }}}
    ],
    [
      'attribute' => 'fest_power', // {{{
      'format' => ['decimal', 1],
      'value' => function (Battle2 $model): ?float {
        return $model->fest_power < 1 ? null : (float)$model->fest_power;
      },
      // }}}
    ],
    [
      'attribute' => 'my_team_estimate_fest_power', // {{{
      'format' => 'integer',
      'value' => function (Battle2 $model): ?int {
        return $model->my_team_estimate_fest_power < 1
            ? null
            : (int)$model->my_team_estimate_fest_power;
      },
      // }}}
    ],
    [
      'attribute' => 'his_team_estimate_fest_power', // {{{
      'format' => 'integer',
      'value' => function (Battle2 $model): ?int {
        return $model->his_team_estimate_fest_power < 1
            ? null
            : (int)$model->his_team_estimate_fest_power;
      },
      // }}}
    ],
    [
      'attribute' => 'fest_power', // {{{
      'format' => 'raw',
      'value' => function (Battle2 $model): ?string {
        $html = FestPowerHistory::widget([
          'current' => $model,
        ]);
        return $html ?: null;
      },
      // }}}
    ],
    [
      'attribute' => 'league_point', // {{{
      'value' => function ($model) : ?string {
        if ($model->league_point < 1) {
          return null;
        }
        return Yii::$app->formatter->asDecimal($model->league_point, 1);
      },
      // }}}
    ],
    [
      'attribute' => 'league_point', // {{{
      'format' => 'raw',
      'value' => function (Battle2 $model) : ?string {
        $html = LeaguePowerHistory::widget([
          'current' => $model,
        ]);
        return $html ?: null;
      },
      // }}}
    ],
    [
      'attribute' => 'estimate_x_power', // {{{
      'value' => function ($model) : ?string {
        if ($model->estimate_x_power === null) {
          return null;
        }

        return Yii::$app->formatter->asInteger($model->estimate_x_power);
      },
      // }}}
    ],
    [
      'attribute' => 'x_power', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if ($model->x_power < 1) {
          if ($model->x_power_after < 1) {
            return null;
          }

          return Yii::$app->formatter->asDecimal($model->x_power_after, 1);
        }

        return implode(' → ', [
          Yii::$app->formatter->asDecimal($model->x_power, 1),
          Yii::$app->formatter->asDecimal($model->x_power_after, 1),
        ]);
      },
      // }}}
    ],
    [
      'attribute' => 'x_power', // {{{
      'format' => 'raw',
      'value' => function (Battle2 $model): ?string {
        $html = XPowerHistory::widget([
          'current' => $model,
        ]);
        return $html ?: null;
      },
      // }}}
    ],
    [
      'attribute' => 'estimate_gachi_power', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if ($model->estimate_gachi_power < 1) {
          return null;
        }
        $max = max(
          (float)$model->estimate_gachi_power,
          (int)$model->my_team_estimate_league_point,
          (int)$model->his_team_estimate_league_point
        );
        return Html::tag(
          'div',
          Html::tag(
            'div',
            Html::encode((string)$model->estimate_gachi_power),
            [
              'class' => [
                'progress-bar',
                'progress-bar-success',
                'progress-bar-striped',
              ],
              'style' => [
                'width' => sprintf('%.2f%%', $model->estimate_gachi_power * 100 / $max),
              ],
            ]
          ),
          ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
        );
      },
      // }}}
    ],
    [
      'attribute' => 'my_team_estimate_league_point', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if ($model->my_team_estimate_league_point < 1) {
          return null;
        }
        $max = max(
          (float)$model->estimate_gachi_power,
          (int)$model->my_team_estimate_league_point,
          (int)$model->his_team_estimate_league_point
        );
        return Html::tag(
          'div',
          Html::tag(
            'div',
            Html::encode((string)$model->my_team_estimate_league_point),
            [
              'class' => [
                'progress-bar',
                'progress-bar-info',
                'progress-bar-striped',
              ],
              'style' => [
                'width' => sprintf('%.2f%%', $model->my_team_estimate_league_point * 100 / $max),
              ],
            ]
          ),
          ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
        );
      },
      // }}}
    ],
    [
      'attribute' => 'his_team_estimate_league_point', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if ($model->his_team_estimate_league_point < 1) {
          return null;
        }
        $max = max(
          (float)$model->estimate_gachi_power,
          (int)$model->my_team_estimate_league_point,
          (int)$model->his_team_estimate_league_point
        );
        return Html::tag(
          'div',
          Html::tag(
            'div',
            Html::encode((string)$model->his_team_estimate_league_point),
            [
              'class' => [
                'progress-bar',
                'progress-bar-danger',
                'progress-bar-striped',
              ],
              'style' => [
                'width' => sprintf('%.2f%%', $model->his_team_estimate_league_point * 100 / $max),
              ],
            ]
          ),
          ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
        );
      },
      // }}}
    ],
    [
      'label' => Yii::t('app', 'Result'), // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        $parts = [];
        if ($model->isGachi && $model->is_knockout !== null) {
          if ($model->is_knockout) {
            $parts[] = Label::widget([
              'content' => Yii::t('app', 'Knockout'),
              'color' => 'info',
            ]);
          } else {
            $parts[] = Label::widget([
              'content' => Yii::t('app', 'Time was up'),
              'color' => 'warning',
            ]);
          }
        }
        if ($model->is_win !== null) {
          $parts[] = ($model->is_win)
            ? Label::widget([
              'content' => Yii::t('app', 'Won'),
              'color' => 'success',
            ])
            : Label::widget([
              'content' => Yii::t('app', 'Lost'),
              'color' => 'danger',
            ]);
        } else {
          $parts[] = Html::encode('?');
        }
        return implode(' ', $parts);
      },
      // }}}
    ],
    [
      'label' => Yii::t('app', 'Team Inked'), // {{{
      'format' => 'raw',
      'value' => function ($model) {
        if ($model->my_team_percent !== null && $model->his_team_percent !== null) {
          $myPct = (float)$model->my_team_percent;
          $hisPct = (float)$model->his_team_percent;
          if ($myPct > 0 || $hisPct > 0) {
            $myDrawPct = round($myPct * 100 / ($myPct + $hisPct) * 100) / 100;
            $myPoint = null;
            $hisPoint = null;
            if ($model->my_team_point !== null && $model->his_team_point !== null) {
              $myPoint = Yii::t('app', '{point}p', ['point' => $model->my_team_point]);
              $hisPoint = Yii::t('app', '{point}p', ['point' => $model->his_team_point]);
            } elseif ($model->map && $model->map->area !== null) {
              $myPoint = Yii::t('app', '~{point}p', [
                'point' => Yii::$app->formatter->asInteger(round($model->map->area * $myPct / 100)),
              ]);
              $hisPoint = Yii::t('app', '~{point}p', [
                'point' => Yii::$app->formatter->asInteger(round($model->map->area * $hisPct / 100)),
              ]);
            }
            return Html::tag(
              'div',
              implode('', [
                Html::tag(
                  'div',
                  Html::encode(Yii::$app->formatter->asPercent($myPct / 100, 1)),
                  [
                    'class' => ['progress-bar', 'progress-bar-info'],
                    'style' => ['width' => sprintf('%.2f%%', $myDrawPct)],
                    'title' => $myPoint,
                  ]
                ),
                Html::tag(
                  'div',
                  Html::encode(Yii::$app->formatter->asPercent($hisPct / 100, 1)),
                  [
                    'class' => ['progress-bar', 'progress-bar-danger', 'text-right'],
                    'style' => ['width' => sprintf('%.2f%%', 100 - $myDrawPct)],
                    'title' => $hisPoint,
                  ]
                )
              ]),
              ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
            );
          }
        }

        if ($model->my_team_point !== null && $model->his_team_point !== null) {
          $myPoint = (int)$model->my_team_point;
          $hisPoint = (int)$model->his_team_point;
          if ($myPoint > 0 || $hisPoint > 0) {
            $myDrawPct = round($myPoint * 100 / ($myPoint + $hisPoint) * 100) / 100;
            return Html::tag(
              'div',
              implode('', [
                Html::tag(
                  'div',
                  Html::encode(Yii::t('app', '{point}p', ['point' => $myPoint])),
                  [
                    'class' => ['progress-bar', 'progress-bar-info'],
                    'style' => ['width' => sprintf('%.2f%%', $myDrawPct)],
                  ]
                ),
                Html::tag(
                  'div',
                  Html::encode(Yii::t('app', '{point}p', ['point' => $hisPoint])),
                  [
                    'class' => ['progress-bar', 'progress-bar-danger', 'text-right'],
                    'style' => ['width' => sprintf('%.2f%%', 100 - $myDrawPct)],
                    'title' => $hisPoint,
                  ]
                )
              ]),
              ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
            );
          }
        }
        return null;
      },
      // }}}
    ],
    [
      'label' => Yii::t('app', 'Final Count'), // {{{
      'format' => 'raw',
      'value' => function ($model) {
        if ($model->my_team_count !== null && $model->his_team_count !== null) {
          $myCount = (int)$model->my_team_count;
          $hisCount = (int)$model->his_team_count;
          if ($myCount > 0 || $hisCount > 0) {
            $myDrawPct = round($myCount * 100 / ($myCount + $hisCount) * 100) / 100;
            return Html::tag(
              'div',
              implode('', [
                Html::tag(
                  'div',
                  Html::encode(Yii::$app->formatter->asInteger($myCount)),
                  [
                    'class' => ['progress-bar', 'progress-bar-info'],
                    'style' => ['width' => sprintf('%.2f%%', $myDrawPct)],
                  ]
                ),
                Html::tag(
                  'div',
                  Html::encode(Yii::$app->formatter->asInteger($hisCount)),
                  [
                    'class' => ['progress-bar', 'progress-bar-danger', 'text-right'],
                    'style' => ['width' => sprintf('%.2f%%', 100 - $myDrawPct)],
                  ]
                )
              ]),
              ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
            );
          }
        }
        return null;
      },
      // }}}
    ],
    'rank_in_team:integer',
    [
      'label' => Yii::t('app', 'Kills / Deaths'), // {{{
      'format' => 'raw',
      'value' => function ($model) {
        return BattleKillDeathColumn::widget([
          'assist' => $model->assist,
          'death' => $model->death,
          'kill' => $model->kill,
          'kill_or_assist' => $model->kill_or_assist,
        ]);
      },
      // }}}
    ],
    'special:integer',
    'max_kill_combo:integer',
    'max_kill_streak:integer',
    [
      'label' => Yii::t('app', 'Cause of Death'),
      'format' => 'raw',
      'value' => fn (Battle2 $model): ?string => ($reasons = $model->battleDeathReasons)
        ? BattleDeathReasonsTable::widget(['reasons' => $reasons])
        : null,
    ],
    [
      'label' => Yii::t('app', 'Turf Inked + Bonus'), // (Nawabari) {{{
      'value' => function ($model) {
        if (!$model->isNawabari) {
          return null;
        }
        $inked = $model->inked;
        if ($model->my_point === null) {
          return null;
        }
        if ($inked === null) {
          return Yii::$app->formatter->asInteger($model->my_point);
        }
        $bonus = $model->my_point - $inked;
        if ($bonus > 0) {
          return sprintf(
            '%sP + %sP',
            Yii::$app->formatter->asInteger($inked),
            Yii::$app->formatter->asInteger($bonus)
          );
        } else {
          return Yii::$app->formatter->asInteger($inked) . 'P';
        }
      },
      // }}}
    ],
    [
      'label' => Yii::t('app', 'Turf Inked'), // (Gachi) {{{
      'value' => function ($model) {
        if ($model->isNawabari) {
          return null;
        }
        $inked = $model->inked;
        if ($model->my_point === null || $inked === null) {
          return null;
        }
        return Yii::$app->formatter->asInteger($inked) . 'P';
      },
      // }}}
    ],
    // cash
    [
      // Gear {{{
      'label' => implode(
        ' ',
        array_filter(
          [
            Html::encode(Yii::t('app', 'Gear')),
            $battle->battleImageGear
              ? (function () use ($battle): ?string {
                PhotoSwipeSimplifyAsset::register($this);
                FontAwesomeAsset::register($this);
                $id = 'img-gear-' . hash('crc32b', __FILE__ . ':' . __LINE__);
                $this->registerCss(Html::renderCss([
                  "#{$id}" => [
                    'width' => '1em',
                    'height' => '1em',
                    'position' => 'relative',
                  ],
                  "#{$id} > *" => [
                    'display' => 'inline-block',
                    'position' => 'absolute',
                    'left' => '0',
                    'top' => '0',
                    'width' => '1em',
                    'height' => '1em',
                  ],
                ]));
                return Html::tag(
                  'div',
                  Html::a(
                    Html::tag(
                      'span',
                      implode('', [
                        Html::img(vsprintf('data:%s,%s', [
                          'image/gif;base64',
                          'R0lGODlhAQABAIABAP///wAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==',
                        ])),
                        (string)FA::fas('image')->fw(),
                      ]),
                      ['id' => $id]
                    ),
                    $battle->battleImageGear->url
                  ),
                  ['data-pswp' => '']
                );
              })()
              : null,
          ],
          function (?string $value): bool {
            return $value !== null && $value !== '' && trim($value) !== '';
          }
        )
      ),
      'format' => 'raw',
      'value' => function (Battle2 $model) : ?string {
        if (
          $model->headgear_id === null &&
          $model->clothing_id === null &&
          $model->shoes_id === null
        ) {
          return null;
        }

        return $this->render('_battle_gear', [
          'battle' => $model,
        ]);
      },
      // }}}
    ],
    'link_url:url', //TODO: easy edit
    [
      'attribute' => 'start_at', // {{{
      'format' => 'raw',
      'value' => function ($model): string {
        if ($model->start_at === null) {
          return '';
        }

        list($intFrom, $intTo) = BattleHelper::periodToRange2($model->period);
        $periodFrom = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
            ->setTimestamp($intFrom);
        $periodTo = $periodFrom->setTimestamp($intTo);

        $dayFrom = (new DateTimeImmutable(
                $model->start_at,
                new DateTimeZone(Yii::$app->timeZone)
            ))
            ->setTime(0, 0, 0); // set to 00:00:00 (midnight)

        $dayTo = $dayFrom
            ->add(new DateInterval('P1D')) // move to next day's 00:00:00
            ->sub(new DateInterval('PT1S')); // back 1 second (23:59:59)

        $fmt = Yii::$app->formatter;
        return implode(' ', array_filter([
          TimestampColumnWidget::widget([
            'value' => $model->start_at,
            'showRelative' => true,
            'formatter' => $fmt,
          ]),
          Html::a(
            (string)FA::fas('calendar-day')->fw(),
            ['show-v2/user',
              'screen_name' => $model->user->screen_name,
              'filter' => [
                'filter' => sprintf('period:%d', $model->period),
              ],
            ],
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app', 'Search {from} - {to}', [
                'from' => $fmt->asDateTime($periodFrom, 'short'),
                'to' => $fmt->asDateTime($periodTo, 'short'),
              ])
            ]
          ),
          Html::a(
            (string)FA::fas('calendar-alt')->fw(),
            ['show-v2/user',
              'screen_name' => $model->user->screen_name,
              'filter' => [
                'term' => 'term',
                'term_from' => $dayFrom->format('Y-m-d H:i:s'),
                'term_to' => $dayTo->format('Y-m-d H:i:s'),
                'timezone' => Yii::$app->timeZone,
              ],
            ],
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app', 'Search {date}', [
                'date' => Yii::$app->formatter->asDate($dayFrom, 'medium'),
              ]),
            ]
          ),
        ]));
      },
      // }}}
    ],
    [
      'attribute' => 'end_at', // {{{
      'format' => 'raw',
      'value' => function ($model): string {
        return TimestampColumnWidget::widget([
          'value' => $model->end_at,
          'showRelative' => true,
        ]);
      },
      // }}}
    ],
    [
      'label' => Yii::t('app', 'Elapsed Time'), // {{{
      'value' => function ($model): ?string {
        if (!$value = $model->elapsedTime) {
          return null;
        }

        return vsprintf('%d:%02d (%s)', [
          (int)floor($value / 60),
          $value % 60,
          Yii::t('app', '{sec,plural,=1{# second} other{# seconds}}', ['sec' => $value]),
        ]);
      },
      // }}}
    ],
    [
      'attribute' => 'created_at', // {{{
      'format' => 'raw',
      'value' => function ($model): string {
        return TimestampColumnWidget::widget([
          'value' => $model->created_at,
          'showRelative' => true,
        ]);
      },
      // }}}
    ],
    [
      'label' => Yii::t('app', 'User Agent'), // {{{
      'format' => 'raw',
      'value' => function ($model) {
        if (!$model->agent) {
          return null;
        }
        return implode(' / ', [
          $model->agent->productUrl
            ? Html::a(
              Html::encode($model->agent->name),
              $model->agent->productUrl,
              ['target' => '_blank', 'rel' => 'nofollow']
            )
            : Html::encode($model->agent->name),
          $model->agent->versionUrl
            ? Html::a(
              Html::encode($model->agent->version),
              $model->agent->versionUrl,
              ['target' => '_blank', 'rel' => 'nofollow']
            )
            : Html::encode($model->agent->version),
        ]);
      },
      // }}}
    ],
    [
      'attribute' => 'ua_variables', // {{{
      'format' => 'raw',
      'value' => function ($model) {
        if ($model->ua_variables === null) {
          return null;
        }
        return Html::tag(
          'table',
          Html::tag(
            'tbody',
            (function (array $rows) {
              $ret = [];
              foreach ($rows as $k => $v) {
                $ret[] = Html::tag(
                  'tr',
                  implode('', [
                    Html::tag('th', Html::encode(Yii::t('app-ua-vars', $k))),
                    Html::tag('td', Html::encode(Yii::t('app-ua-vars-v', $v))),
                  ])
                );
              }
              return implode('', $ret);
            })($model->extraData)
          ),
          ['class' => 'table', 'style' => 'margin-bottom:0']
        );
      },
      // }}}
    ],
    'note:ntext',
    [
      'attribute' => 'private_note', // {{{
      'format' => 'raw',
      'value' => function ($model) {
        if ($model->private_note == '') {
          return null;
        }
        if (Yii::$app->user->isGuest || Yii::$app->user->identity->id != $model->user_id) {
          return null;
        }
        $this->registerCss('#private-note{display:none}');
        $this->registerJs(
            '!function(a){"use strict";var o=a("#private-note-show"),e=a("#private-note"),n=a(".fa",o);' .
            'o.hover(function(){n.removeClass("fa-lock").addClass("fa-unlock-alt")},function(){' .
            'n.removeClass("fa-unlock-alt").addClass("fa-lock")}).click(function(){o.hide(),e.show()})}(jQuery);'
        );
        return implode('', [
          Html::button(
            Html::tag('span', '', ['class' => 'fa fa-lock fa-fw']),
            ['class' => 'btn btn-default', 'id' => 'private-note-show']
          ),
          Html::tag(
            'div',
            Yii::$app->formatter->asNtext($model->private_note),
            ['id' => 'private-note']
          ),
        ]);
      },
      // }}}
    ],
    [
      'label' => Yii::t('app', 'Game Version'), // {{{
      'value' => function ($model) {
        return $model->version->name ?? Yii::t('app', 'Unknown');
      },
      // }}}
    ],
    [
      'label' => Yii::t('app', 'Stats'), // {{{
      'format' => 'raw',
      'value' => function (Battle2 $model): string {
        $lobby = $model->lobby->key ?? null;
        $mode = $model->mode->key ?? null;
        if (!$lobby || !$mode || !$model->weapon || !$model->map) {
          return implode('', [
            Html::tag('span', (string)FA::fas('times')->fw(), ['class' => 'text-danger']),
            Html::encode(Yii::t('app', 'Incomplete Data')),
          ]);
        }

        if ($lobby === 'private' || $mode === 'private') {
          return implode('', [
            Html::tag('span', (string)FA::fas('times')->fw(), ['class' => 'text-danger']),
            Html::encode(Yii::t('app-rule2', 'Private Battle')),
          ]);
        }

        $f = function (string $label, bool $value): string {
          return vsprintf('%s: %s%s', [
            Html::encode($label),
            $value
              ? Html::tag('span', (string)FA::fas('check')->fw(), ['class' => 'text-success'])
              : Html::tag('span', (string)FA::fas('times')->fw(), ['class' => 'text-danger']),
            Html::encode(Yii::t('yii', $value ? 'Yes' : 'No')),
          ]);
        };
        return implode('<br>', [
          $f(Yii::t('app', 'Automated'), $model->is_automated),
          $f(Yii::t('app', 'Used in global stats'), $model->is_automated && $model->use_for_entire),
        ]);
      },
      // }}}
    ],
  ],
]) . "\n" ?>
