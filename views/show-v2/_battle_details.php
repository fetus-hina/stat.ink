<?php
use app\assets\BattleEditAsset;
use app\models\Battle2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->registerCss('#battle .progress{margin-bottom:0}');
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
      'value' => function ($model) {
        $weapon = $model->weapon;
        return sprintf(
          '%s (%s / %s)',
          Yii::t('app-weapon2', $weapon->name ?? '?'),
          Yii::t('app-subweapon2', $weapon->subweapon->name ?? '?'),
          Yii::t('app-special2', $weapon->special->name ?? '?')
        );
      },
      // }}}
    ],
    [
      'attribute' => 'rank_id', // {{{
      'value' => function ($model) : ?string {
        if ($model->rank_id === null && $model->rank_after_id === null) {
          return null;
        }
        return sprintf(
          '%s%s → %s%s',
          Yii::t('app-rank2', $model->rank->name ?? '?'),
          ($model->rank_exp !== null && ($model->rank->key ?? '') === 's+')
            ? (' ' . $model->rank_exp)
            : '',
          Yii::t('app-rank2', $model->rankAfter->name ?? '?'),
          ($model->rank_after_exp !== null && ($model->rankAfter->key ?? '') === 's+')
            ? (' ' . $model->rank_after_exp)
            : ''
        );
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
      'attribute' => 'fest_title_id', // {{{
      'value' => function ($model) : ?string {
        $title1 = $model->festTitle;
        $title2 = $model->festTitleAfter;
        if (!$title1 && !$title2) {
          return null;
        }
        $gender = $model->gender;
        $format = function ($title, $exp) use ($model, $gender) : string {
          if (!$title) {
            return '?';
          }
          $name = Yii::t('app-fest', $title->getName($gender), ['***', '***']);
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
      'attribute' => 'fest_power', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if ($model->fest_power < 1) {
          return null;
        }
        $max = max(
          (float)$model->fest_power,
          (int)$model->my_team_estimate_fest_power,
          (int)$model->his_team_estimate_fest_power
        );
        return Html::tag(
          'div',
          Html::tag(
            'div',
            Html::encode((string)$model->fest_power),
            [
              'class' => [
                'progress-bar',
                'progress-bar-success',
                'progress-bar-striped',
              ],
              'style' => [
                'width' => sprintf('%.2f%%', $model->fest_power * 100 / $max),
              ],
            ]
          ),
          ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
        );
      },
      // }}}
    ],
    [
      'attribute' => 'my_team_estimate_fest_power', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if ($model->my_team_estimate_fest_power < 1) {
          return null;
        }
        $max = max(
          (float)$model->fest_power,
          (int)$model->my_team_estimate_fest_power,
          (int)$model->his_team_estimate_fest_power
        );
        return Html::tag(
          'div',
          Html::tag(
            'div',
            Html::encode((string)$model->my_team_estimate_fest_power),
            [
              'class' => [
                'progress-bar',
                'progress-bar-info',
                'progress-bar-striped',
              ],
              'style' => [
                'width' => sprintf('%.2f%%', $model->my_team_estimate_fest_power * 100 / $max),
              ],
            ]
          ),
          ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
        );
      },
      // }}}
    ],
    [
      'attribute' => 'his_team_estimate_fest_power', // {{{
      'format' => 'raw',
      'value' => function ($model) : ?string {
        if ($model->his_team_estimate_fest_power < 1) {
          return null;
        }
        $max = max(
          (float)$model->fest_power,
          (int)$model->my_team_estimate_fest_power,
          (int)$model->his_team_estimate_fest_power
        );
        return Html::tag(
          'div',
          Html::tag(
            'div',
            Html::encode((string)$model->his_team_estimate_fest_power),
            [
              'class' => [
                'progress-bar',
                'progress-bar-danger',
                'progress-bar-striped',
              ],
              'style' => [
                'width' => sprintf('%.2f%%', $model->his_team_estimate_fest_power * 100 / $max),
              ],
            ]
          ),
          ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
        );
      },
      // }}}
    ],
    [
      'attribute' => 'league_point',
      'value' => function ($model) : ?string {
        if ($model->league_point < 1) {
          return null;
        }
        return Yii::$app->formatter->asDecimal($model->league_point, 1);
      }
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
            $parts[] = Html::tag(
              'span',
              Html::encode(Yii::t('app', 'Knockout')),
              ['class' => 'label label-info']
            );
          } else {
            $parts[] = Html::tag(
              'span',
              Html::encode(Yii::t('app', 'Time was up')),
              ['class' => 'label label-warning']
            );
          }
        }
        if ($model->is_win !== null) {
          $parts[] = ($model->is_win)
            ? Html::tag(
              'span',
              Html::encode(Yii::t('app', 'Won')),
              ['class' => 'label label-success']
            )
            : Html::tag(
              'span',
              Html::encode(Yii::t('app', 'Lost')),
              ['class' => 'label label-danger']
            );
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
            } elseif ($model->map->area !== null) {
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
    'rank_in_team',
    [
      'label' => Yii::t('app', 'Kills / Deaths'), // {{{
      'format' => 'raw',
      'value' => function ($model) {
        $parts = [];
        $parts[] = Html::encode(sprintf(
          '%s / %s',
          $model->kill === null ? '?' : $model->kill,
          $model->death === null ? '?' : $model->death
        ));
        if ($model->kill !== null && $model->death !== null) {
          if ($model->kill > $model->death) {
            $parts[] = Html::tag('span', Html::encode('>'), ['class' => 'label label-success']);
          } elseif ($model->kill < $model->death) {
            $parts[] = Html::tag('span', Html::encode('<'), ['class' => 'label label-danger']);
          } else {
            $parts[] = Html::tag('span', Html::encode('='), ['class' => 'label label-default']);
          }
        }
        return implode(' ', $parts);
      },
      // }}}
    ],
    [
      'label' => Yii::t('app', 'Kills+Assist / Specials'), // {{{
      'format' => 'raw',
      'value' => function ($model) {
        if ($model->kill_or_assist === null && $model->special === null) {
          return null;
        }
        return sprintf(
          '%s / %s',
          $model->kill_or_assist === null ? '?' : $model->kill_or_assist,
          $model->special === null ? '?' : $model->special
        );
      },
      // }}}
    ],
    [
      'attribute' => 'kill_ratio', // {{{
      'value' => function ($model) {
        if ($model->kill === null || $model->death === null) {
          return null;
        }
        return $model->kill_ratio === null
          ? Yii::t('app', 'N/A')
          : Yii::$app->formatter->asDecimal($model->kill_ratio, 2);
      },
      // }}}
    ],
    [
      'attribute' => 'kill_rate', // {{{
      'value' => function ($model) {
        if ($model->kill === null || $model->death === null) {
          return null;
        }
        return $model->kill_rate === null
          ? Yii::t('app', 'N/A')
          : Yii::$app->formatter->asPercent($model->kill_rate / 100, 1);
      },
      // }}}
    ],
    'max_kill_combo:integer',
    'max_kill_streak:integer',
    [
      'label' => Yii::t('app', 'Cause of Death'), // FIXME {{{
      'value' => function ($model) {
        $reasons = $model->getBattleDeathReasons()
          ->orderBy('{{battle_death_reason2}}.[[count]] DESC')
          ->all();
        if (!$reasons) {
          return null;
        }
        return null;
        // <tr>
        //   <th>{{'Cause of Death'|translate:'app'|escape}}</th>
        //   <td>
        //     <table>
        //       <tbody>
        //         {{foreach $deathReasons as $deathReason}}
        //           <tr>
        //             <td>{{$deathReason->reason->translatedName|default:'?'|escape}}</td>
        //             <td style="padding:0 10px">:</td>
        //             <td>
        //               {{$params = ['n' => $deathReason->count, 'nFormatted' => $app->formatter->asDecimal($deathReason->count)]}}
        //               {{"{nFormatted} {n, plural, =1{time} other{times}}"|translate:'app':$params|escape}}
        //             </td>
        //           </tr>
        //         {{/foreach}}
        //       </tbody>
        //     </table>
        //   </td>
        // </tr>
      },
      // }}}
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
              ? Html::a(
                Html::tag('span', '', ['class' => 'fa fa-image']),
                $battle->battleImageGear->url,
                ['class' => 'swipebox']
              )
              : null,
          ],
          function ($value) : bool {
            return $value !== null && $value !== '' && trim($value) !== '';
          }
        )
      ),
      'format' => 'raw',
      'value' => function (Battle2 $model) : ?string {
        if ($model->headgear_id === null &&
          $model->clothing_id === null &&
          $model->shoes_id === null)
        {
          return null;
        }

        return $this->render('_battle_gear', [
          'headgear' => $model->headgear,
          'clothing' => $model->clothing,
          'shoes' => $model->shoes,
        ]);
      },
      // }}}
    ],
    'link_url:url', //TODO: easy edit
    [
      'attribute' => 'start_at', // {{{
      'format' => 'raw',
      'value' => function ($model) {
        if ($model->start_at === null) {
          return null;
        }
        return sprintf(
          '%s (%s)',
          Yii::$app->formatter->asHtmlDatetime($model->start_at),
          Html::encode(Yii::$app->formatter->asRelativeTime($model->start_at))
        );
      },
      // }}}
    ],
    [
      'attribute' => 'end_at', // {{{
      'format' => 'raw',
      'value' => function ($model) {
        if ($model->end_at === null) {
          return null;
        }
        return sprintf(
          '%s (%s)',
          Yii::$app->formatter->asHtmlDatetime($model->end_at),
          Html::encode(Yii::$app->formatter->asRelativeTime($model->end_at))
        );
      },
      // }}}
    ],
    [
      'attribute' => 'created_at', // {{{
      'format' => 'raw',
      'value' => function ($model) {
        if ($model->created_at === null) {
          return null;
        }
        return sprintf(
          '%s (%s)',
          Yii::$app->formatter->asHtmlDatetime($model->created_at),
          Html::encode(Yii::$app->formatter->asRelativeTime($model->created_at))
        );
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
  ],
]) . "\n" ?>
