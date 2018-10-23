<?php
use app\assets\AppLinkAsset;
use app\assets\UserMiniinfoAsset;
use app\components\widgets\JdenticonWidget;
use app\components\widgets\MiniinfoUserLink;
use app\models\Rank2;
use app\models\Rule2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;

UserMiniinfoAsset::register($this);

$_icon = AppLinkAsset::register($this);
$_st = $user->userStat2;
$fmt = Yii::$app->formatter;
?>
<div id="user-miniinfo" itemscope itemtype="http://schema.org/Person" itemprop="author">
  <div id="user-miniinfo-box">
    <h2>
      <?= Html::a(
        implode('', [
          Html::tag(
            'span',
            $user->userIcon
              ? Html::img(
                $user->userIcon->url,
                ['width' => '48', 'height' => '48']
              )
              : JdenticonWidget::widget([
                'hash' => $user->identiconHash,
                'class' => 'identicon',
                'size' => '48',
                'schema' => 'image',
              ]),
            ['class' => 'miniinfo-user-icon']
          ),
          Html::tag(
            'span',
            Html::encode($user->name),
            ['class' => 'miniinfo-user-name', 'itemprop' => 'name']
          ),
        ]),
        ['/show-user/profile', 'screen_name' => $user->screen_name]
      ) . "\n" ?>
    </h2>
<?php if ($_st): ?>
<?php $nbsp = chr(0xc2) . chr(0xa0) ?>
    <div class="row">
<?php // 合計 {{{ ?>
      <?= DetailView::widget([
        'options' => [
          'tag' => 'div',
        ],
        'model' => $_st,
        'template' => "\n" . Html::tag(
          'div',
          implode("\n", [
            Html::tag('div', '{label}', ['class' => 'user-label auto-tooltip', 'title' => '{label}']),
            Html::tag('div', '{value}', ['class' => 'user-number']),
          ]),
          ['class' => 'col-xs-4']
        ) . "\n",
        'attributes' => [
          [
            'label' => Yii::t('app', 'Battles'),
            'format' => 'raw',
            'value' => function ($model) use ($user, $fmt) : string {
              return Html::a(
                Html::encode($fmt->asInteger($model->battles)),
                ['show-v2/user', 'screen_name' => $user->screen_name]
              );
            },
          ],
          [
            'label' => Yii::t('app', 'Win %'),
            'value' => function ($model) use ($fmt) : string {
              return $model->have_win_lose < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asPercent($model->win_battles / $model->have_win_lose, 1);
            },
          ],
          [
            'label' => Yii::t('app', 'Last {n} Battles', ['n' => 50]),
            'value' => $nbsp,
          ],
          [
            'label' => Yii::t('app', 'Avg Kills'),
            'value' => function ($model) use ($fmt) : string {
              return $model->have_kill_death < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->kill / $model->have_kill_death, 2);
            },
          ],
          [
            'label' => Yii::t('app', 'Avg Deaths'),
            'value' => function ($model) use ($fmt) : string {
              return $model->have_kill_death < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->death / $model->have_kill_death, 2);
            },
          ],
          [
            'label' => Yii::t('app', 'Kill Ratio'),
            'value' => function ($model) use ($fmt) : string {
              if ($model->have_kill_death < 1) {
                return Yii::t('app', 'N/A');
              }
              if ($model->death == 0) {
                if ($model->kill == 0) {
                  return Yii::t('app', 'N/A');
                } else {
                  return $fmt->asDecimal(99.99, 2);
                }
              }
              return $fmt->asDecimal($model->kill / $model->death, 2);
            },
          ],
          [
            'label' => Yii::t('app', 'Kills/min'),
            'value' => function ($model) use ($fmt) : string {
              return $model->have_kill_death_time < 1 || $model->total_seconds < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->kill_with_time * 60 / $model->total_seconds, 3);
            },
          ],
          [
            'label' => Yii::t('app', 'Deaths/min'),
            'value' => function ($model) use ($fmt) : string {
              return $model->have_kill_death_time < 1 || $model->total_seconds < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->death_with_time * 60 / $model->total_seconds, 3);
            },
          ],
          [
            'label' => Yii::t('app', 'Kill Rate'),
            'value' => function ($model) use ($fmt) : string {
              if ($model->have_kill_death < 1) {
                return Yii::t('app', 'N/A');
              }
              if ($model->death == 0 && $model->kill == 0) {
                  return Yii::t('app', 'N/A');
              }
              return $fmt->asPercent($model->kill / ($model->kill + $model->death), 1);
            },
          ],
        ],
      ]) . "\n" ?>
<?php // }}} ?>
    </div>
    <hr>
    <div class="row">
      <div class="col-xs-12">
        <div class="user-label">
          <?= Html::encode(Yii::t('app-rule2', 'Turf War')) . "\n" ?>
        </div>
      </div>
<?php // ナワバリ {{{ ?>
      <?= DetailView::widget([
        'options' => [
          'tag' => 'div',
        ],
        'model' => $_st,
        'template' => "\n" . Html::tag(
          'div',
          implode("\n", [
            Html::tag('div', '{label}', ['class' => 'user-label auto-tooltip', 'title' => '{label}']),
            Html::tag('div', '{value}', ['class' => 'user-number']),
          ]),
          ['class' => 'col-xs-4']
        ) . "\n",
        'attributes' => [
          [
            'label' => Yii::t('app', 'Battles'),
            'format' => 'integer',
            'attribute' => 'turf_battles',
          ],
          [
            'label' => Yii::t('app', 'Win %'),
            'value' => function ($model) use ($fmt) : string {
              return $model->turf_have_win_lose < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asPercent($model->turf_win_battles / $model->turf_have_win_lose, 1);
            },
          ],
          [
            'label' => Yii::t('app', 'Last {n} Battles', ['n' => 50]),
            'value' => $nbsp,
          ],
          [
            'label' => Yii::t('app', 'Avg Kills'),
            'value' => function ($model) use ($fmt) : string {
              return $model->turf_have_kill_death < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->turf_kill / $model->turf_have_kill_death, 2);
            },
          ],
          [
            'label' => Yii::t('app', 'Avg Deaths'),
            'value' => function ($model) use ($fmt) : string {
              return $model->turf_have_kill_death < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->turf_death / $model->turf_have_kill_death, 2);
            },
          ],
          [
            'label' => Yii::t('app', 'Kill Ratio'),
            'value' => function ($model) use ($fmt) : string {
              if ($model->turf_have_kill_death < 1) {
                return Yii::t('app', 'N/A');
              }
              if ($model->turf_death == 0) {
                if ($model->turf_kill == 0) {
                  return Yii::t('app', 'N/A');
                } else {
                  return $fmt->asDecimal(99.99, 2);
                }
              }
              return $fmt->asDecimal($model->turf_kill / $model->turf_death, 2);
            },
          ],
          [
            'label' => Yii::t('app', 'Total Inked'),
            'format' => 'raw',
            'value' => function ($model) use ($fmt) : string {
              return $model->turf_have_inked < 1
                ? Html::encode(Yii::t('app', 'N/A'))
                : Html::tag(
                  'span',
                  Html::encode((function ($value) use ($fmt) {
                      if ($value >= 1e24) return $fmt->asInteger((int)floor($value / 1e24)) . 'Y';
                      if ($value >= 1e21) return $fmt->asInteger((int)floor($value / 1e21)) . 'Z';
                      if ($value >= 1e18) return $fmt->asInteger((int)floor($value / 1e18)) . 'E';
                      if ($value >= 1e15) return $fmt->asInteger((int)floor($value / 1e15)) . 'P';
                      if ($value >= 1e12) return $fmt->asInteger((int)floor($value / 1e12)) . 'T';
                      if ($value >= 1e9) return $fmt->asInteger((int)floor($value / 1e9)) . 'G';
                      if ($value >= 1e6) return $fmt->asInteger((int)floor($value / 1e6)) . 'M';
                      if ($value >= 1e3) return $fmt->asInteger((int)floor($value / 1e3)) . 'k';
                      return $fmt->asInteger($value);
                  })($model->turf_total_inked)),
                  [
                    'class' => 'auto-tooltip',
                    'title' => Yii::t('app', '{point, plural, other{#p}}', [
                      'point' => $model->turf_total_inked,
                    ]),
                  ]
                );
            },
          ],
          [
            'label' => Yii::t('app', 'Avg Inked'),
            'value' => function ($model) use ($fmt) : string {
              return $model->turf_have_inked < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->turf_total_inked / $model->turf_have_inked, 1);
            },
          ],
          [
            'label' => Yii::t('app', 'Max Inked'),
            'value' => function ($model) use ($fmt) : string {
              return $model->turf_have_inked < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asInteger($model->turf_max_inked);
            },
          ],
        ],
      ]) . "\n" ?>
<?php // }}} ?>
    </div>
    <hr>
    <div class="row">
      <div class="col-xs-12">
        <div class="user-label">
          <?= Html::encode(Yii::t('app-rule2', 'Ranked Battle')) . "\n" ?>
        </div>
      </div>
<?php // ガチ {{{ ?>
      <?= DetailView::widget([
        'options' => [
          'tag' => 'div',
        ],
        'model' => $_st,
        'template' => function ($attribute, $index, $widget) : string {
          static $standard = null;
          static $peak = null;
          if ($standard === null) {
            $standard = Html::tag(
              'div',
              implode("\n", [
                Html::tag('div', '{label}', ['class' => 'user-label auto-tooltip', 'title' => '{label}']),
                Html::tag('div', '{value}', ['class' => 'user-number']),
              ]),
              ['class' => 'col-xs-4']
            );
          }
          if ($peak === null) {
            $peak = Html::tag(
              'div',
              implode("\n", [
                Html::tag('div', '{label}', ['class' => 'user-label auto-tooltip', 'title' => '{label}']),
                Html::tag('div', '{value}', ['class' => 'user-number']),
              ]),
              ['class' => 'col-xs-6']
            );
          }
          $captionOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'captionOptions', []));
          $contentOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'contentOptions', []));
          return strtr(
            preg_match('/_rank_peak$/', $attribute['attribute'] ?? '') ? $peak : $standard,
            [
              '{label}' => $attribute['label'],
              '{value}' => $widget->formatter->format($attribute['value'], $attribute['format']),
              '{captionOptions}' => $captionOptions,
              '{contentOptions}' => $contentOptions,
            ]
          );
        },
        'attributes' => [
          [
            'label' => Yii::t('app', 'Battles'),
            'format' => 'integer',
            'attribute' => 'gachi_battles',
          ],
          [
            'label' => Yii::t('app', 'Win %'),
            'value' => function ($model) use ($fmt) : string {
              return $model->gachi_have_win_lose < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asPercent($model->gachi_win_battles / $model->gachi_have_win_lose, 1);
            },
          ],
          [
            'label' => Yii::t('app', 'Last {n} Battles', ['n' => 50]),
            'value' => $nbsp,
          ],
          [
            'label' => Yii::t('app', 'Avg Kills'),
            'value' => function ($model) use ($fmt) : string {
              return $model->gachi_have_kill_death < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->gachi_kill / $model->gachi_have_kill_death, 2);
            },
          ],
          [
            'label' => Yii::t('app', 'Avg Deaths'),
            'value' => function ($model) use ($fmt) : string {
              return $model->gachi_have_kill_death < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->gachi_death / $model->gachi_have_kill_death, 2);
            },
          ],
          [
            'label' => Yii::t('app', 'Kill Ratio'),
            'value' => function ($model) use ($fmt) : string {
              if ($model->gachi_have_kill_death < 1) {
                return Yii::t('app', 'N/A');
              }
              if ($model->gachi_death == 0) {
                if ($model->gachi_kill == 0) {
                  return Yii::t('app', 'N/A');
                } else {
                  return $fmt->asDecimal(99.99, 2);
                }
              }
              return $fmt->asDecimal($model->gachi_kill / $model->gachi_death, 2);
            },
          ],
          [
            'label' => Yii::t('app', 'Kills/min'),
            'value' => function ($model) use ($fmt) : string {
              return $model->gachi_kill_death_time < 1 || $model->gachi_total_seconds < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->gachi_kill_with_time * 60 / $model->gachi_total_seconds, 3);
            },
          ],
          [
            'label' => Yii::t('app', 'Deaths/min'),
            'value' => function ($model) use ($fmt) : string {
              return $model->gachi_kill_death_time < 1 || $model->total_seconds < 1
                ? Yii::t('app', 'N/A')
                : $fmt->asDecimal($model->gachi_death_with_time * 60 / $model->gachi_total_seconds, 3);
            },
          ],
          [
            'label' => Yii::t('app', 'Kill Rate'),
            'value' => function ($model) use ($fmt) : string {
              if ($model->gachi_have_kill_death < 1) {
                return Yii::t('app', 'N/A');
              }
              if ($model->gachi_death == 0 && $model->gachi_kill == 0) {
                return Yii::t('app', 'N/A');
              }
              return $fmt->asPercent($model->gachi_kill / ($model->gachi_kill + $model->gachi_death), 1);
            },
          ],
          [
            'attribute' => 'area_rank_peak',
            'label' => Yii::t('app', '{rule}: Peak', [
              'rule' => Yii::t('app-rule2', 'SZ'),
            ]),
            'value' => function ($model) use ($fmt, $nbsp) : string {
              if ($model->gachi_battles < 1) {
                return Yii::t('app', 'N/A');
              }
              $string = Rank2::renderRank($model->area_rank_peak);
              if (!$string) {
                return Yii::t('app', 'N/A');
              }
              return $string;
            },
          ],
          [
            'attribute' => 'yagura_rank_peak',
            'label' => Yii::t('app', '{rule}: Peak', [
              'rule' => Yii::t('app-rule2', 'TC'),
            ]),
            'value' => function ($model) use ($fmt, $nbsp) : string {
              if ($model->gachi_battles < 1) {
                return Yii::t('app', 'N/A');
              }
              $string = Rank2::renderRank($model->yagura_rank_peak);
              if (!$string) {
                return Yii::t('app', 'N/A');
              }
              return $string;
            },
          ],
          [
            'attribute' => 'hoko_rank_peak',
            'label' => Yii::t('app', '{rule}: Peak', [
              'rule' => Yii::t('app-rule2', 'RM'),
            ]),
            'value' => function ($model) use ($fmt, $nbsp) : string {
              if ($model->gachi_battles < 1) {
                return Yii::t('app', 'N/A');
              }
              $string = Rank2::renderRank($model->hoko_rank_peak);
              if (!$string) {
                return Yii::t('app', 'N/A');
              }
              return $string;
            },
          ],
          [
            'attribute' => 'asari_rank_peak',
            'label' => Yii::t('app', '{rule}: Peak', [
              'rule' => Yii::t('app-rule2', 'CB'),
            ]),
            'value' => function ($model) use ($fmt, $nbsp) : string {
              if ($model->gachi_battles < 1) {
                return Yii::t('app', 'N/A');
              }
              $string = Rank2::renderRank($model->asari_rank_peak);
              if (!$string) {
                return Yii::t('app', 'N/A');
              }
              return $string;
            },
          ],
        ],
      ]) . "\n" ?>
<?php // }}} ?>
    </div>
<?php endif; ?>
    <div class="miniinfo-databox">
      <?= implode('<br>', array_merge(
        [
          Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'fa fa-fw fa-chart-pie']),
              Html::encode(Yii::t('app', 'Stats ({rule})', [
                'rule' => Yii::t('app-rule2', 'Turf War'),
              ])),
            ]),
            ['show-v2/user-stat-nawabari',
              'screen_name' => $user->screen_name,
            ]
          ),
        ],
        array_map(
          function (Rule2 $rule) use ($user): string {
            return Html::a(
              implode('', [
                Html::tag('span', '', ['class' => 'fa fa-fw fa-chart-pie']),
                Html::encode(Yii::t('app', 'Stats ({rule})', [
                  'rule' => Yii::t('app-rule2', $rule->name),
                ])),
              ]),
              ['show-v2/user-stat-gachi',
                'screen_name' => $user->screen_name,
                'rule' => $rule->key,
              ]
            );
          },
          Rule2::find()->where(['not', ['key' => 'nawabari']])->orderBy(['id' => SORT_ASC])->all()
        ),
        [
          Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'fa fa-fw fa-chart-pie']),
              Html::encode(Yii::t('app', 'Stats (by Mode and Stage)')),
            ]),
            ['show-v2/user-stat-by-map-rule', 'screen_name' => $user->screen_name]
          ),
          Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'fa fa-fw fa-chart-pie']),
              Html::encode(Yii::t('app', 'Stats (by Weapon)')),
            ]),
            ['show-v2/user-stat-by-weapon', 'screen_name' => $user->screen_name]
          ),
          Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'fa fa-fw fa-chart-pie']),
              Html::encode(Yii::t('app', 'Daily Report')),
            ]),
            ['show-v2/user-stat-report', 'screen_name' => $user->screen_name]
          ),
        ])) . "\n" ?>
    </div>
    <?= MiniinfoUserLink::widget(['user' => $user]) . "\n" ?>
  </div>
</div>
