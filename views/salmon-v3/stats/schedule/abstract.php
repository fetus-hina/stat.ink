<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\OverfishingTrait;
use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Map3;
use app\models\Salmon3;
use app\models\SalmonEvent3;
use app\models\SalmonSchedule3;
use app\models\SalmonScheduleWeapon3;
use app\models\SalmonWaterLevel2;
use app\models\SplatoonVersion3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @phpstan-import-type OverfishingStats from OverfishingTrait
 *
 * @var Map3|SalmonMap3|null $map
 * @var OverfishingStats|null $overfishing
 * @var SalmonSchedule3|null $schedule
 * @var SplatoonVersion3|null $version
 * @var User $user
 * @var View $this
 * @var array<int, SalmonEvent3> $events
 * @var array<int, SalmonWaterLevel2> $tides
 * @var array<string, scalar|null> $stats
 */

$fmt = clone Yii::$app->formatter;
$fmt->nullDisplay = '-';

$totalAndAvg = fn (string $attrTotal, string $attrAvg, string $attrSD): string => sprintf(
  '%s (σ=%s) / %s: %s',
  Html::tag(
    'span',
    $fmt->asDecimal(
      TypeHelper::floatOrNull(ArrayHelper::getValue($stats, $attrAvg)),
      2,
    ) ?: '-',
    [
      'class' => 'auto-tooltip',
      'title' => Yii::t('app', 'Average'),
    ],
  ),
  Html::tag(
    'span',
    $fmt->asDecimal(
      TypeHelper::floatOrNull(ArrayHelper::getValue($stats, $attrSD)),
      2,
    ) ?: '-',
    [
      'class' => 'auto-tooltip',
      'title' => Yii::t('app', 'Standard Deviation'),
    ],
  ),
  Yii::t('app', 'Total'),
  $fmt->asInteger(TypeHelper::intOrNull(ArrayHelper::getValue($stats, $attrTotal))) ?: '-',
);

echo DetailView::widget([
  'model' => $stats,
  'options' => [
    'class' => [
      'detail-view',
      'table',
      'table-bordered',
      'table-condensed',
      'table-striped',
      'w-auto',
    ],
  ],
  'attributes' => array_filter(
    [
      $schedule
        ? [
          'label' => Yii::t('app', 'Lobby'),
          'format' => 'raw',
          'value' => match (true) {
            $schedule->big_map_id !== null => implode(' ', [
              Icon::s3BigRun(),
              Html::encode(Yii::t('app-salmon3', 'Big Run')),
            ]),
            $schedule->is_eggstra_work => implode(' ', [
              Icon::s3Eggstra(),
              Html::encode(Yii::t('app-salmon3', 'Eggstra Work')),
            ]),
            $schedule->map_id !== null => implode(' ', [
              Icon::s3Salmon(),
              Html::encode(Yii::t('app-salmon3', 'Normal Job')),
            ]),
            default => null,
          },
        ]
        : null,
      $schedule?->salmonScheduleWeapon3s
        ? [
          'label' => Yii::t('app-salmon3', 'Supplied Weapons'),
          'format' => 'raw',
          'value' => implode(' ', array_map(
            fn (SalmonScheduleWeapon3 $model): ?string => match (true) {
              $model->weapon !== null => Icon::s3Weapon($model->weapon)
                ?? Html::tag('span', Icon::unknown(), [
                  'class' => 'auto-tooltip',
                  'title' => Yii::t('app-weapon3', $model->weapon?->name ?? '?'),
                ]),
              $model->random !== null => Icon::s3SalmonRandom($model->random),
              default => Icon::unknown(),
            },
            $schedule->salmonScheduleWeapon3s,
          )),
        ]
        : null,
      [
        'label' => Yii::t('app-salmon2', 'Jobs'),
        'format' => 'raw',
        'value' => implode(' ', [
          $fmt->asInteger(ArrayHelper::getValue($stats, 'count')),
          Html::a(
            Icon::search(),
            ['salmon-v3/index',
              'screen_name' => $user->screen_name,
              'f' => [
                'lobby' => match (true) {
                  $schedule?->big_map_id !== null => 'bigrun',
                  $schedule?->is_eggstra_work => 'eggstra',
                  default => 'normal',
                },
                'term' => $schedule ? 'term' : null,
                'term_from' => $schedule ? sprintf('@%d', strtotime($schedule->start_at)) : null,
                'term_to' => $schedule ? sprintf('@%d', strtotime($schedule->end_at)) : null,
              ],
            ],
          ),
        ]),
      ],
      [
        'label' => Yii::t('app-salmon2', 'Clear %'),
        'format' => 'raw',
        'value' => vsprintf('%s (%s: %s)', [
          Html::tag(
            'span',
            Html::encode(
              $fmt->asPercent(
                TypeHelper::int(ArrayHelper::getValue($stats, 'cleared')) /
                  TypeHelper::int(ArrayHelper::getValue($stats, 'count')),
                1,
              ),
            ),
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app', '{nFormatted} {n, plural, =1{time} other{times}}', [
                'n' => TypeHelper::int(ArrayHelper::getValue($stats, 'cleared')),
                'nFormatted' => $fmt->asInteger(
                  TypeHelper::int(ArrayHelper::getValue($stats, 'cleared')),
                ),
              ]),
            ],
          ),
          Html::encode(Yii::t('app-salmon2', 'Avg. Waves')),
          Html::encode(
            match (is_float($v = TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'avg_waves')))) {
              true => $fmt->asDecimal($v, 2),
              default => '-',
            },
          ),
        ]),
      ],
      [
        'label' => Yii::t('app-salmon3', 'King Salmonid Defeat Rate'),
        'format' => 'raw',
        'value' => trim(
          implode(' ', [
            version_compare($version?->tag ?? '0.0.0', '6.0.0', '>=') && $schedule?->king
              ? Icon::s3BossSalmonid($schedule->king)
              : '',
            Html::encode(
              match ($v = TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'king_appears'))) {
                0, null => Yii::t('app', 'N/A'),
                default => vsprintf('%s (%s / %s)', [
                  $fmt->asPercent(
                    TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'king_defeated')) / $v,
                    1,
                  ),
                  $fmt->asInteger((int)TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'king_defeated'))),
                  $fmt->asInteger($v),
                ]),
              },
            ),
          ]),
        ),
      ],
      [
        'label' => Yii::t('app-salmon3', 'Max. Hazard Level (cleared)'),
        'format' => 'raw',
        'value' => match ($v = TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'max_danger_rate'))) {
          null => '-',
          default => $v >= 333
            ? vsprintf('%s %s', [
              Icon::s3HazardLevelMax(),
              $fmt->asPercent(3.33, 0),
            ])
            : $fmt->asPercent(((int)$v) / 100, 0),
        },
      ],
      [
        'label' => vsprintf('%s (%s)', [
          Yii::t('app', 'Maximum'),
          Yii::t('app-salmon3', 'Team Total'),
        ]),
        'format' => 'raw',
        'value' => implode('', [
          Html::tag(
            'span',
            implode(' ', [
              Icon::goldenEgg(),
              $fmt->asInteger(TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'max_golden'))),
            ]),
            ['class' => 'nobr mr-3'],
          ),
          Html::tag(
            'span',
            implode(' ', [
              Icon::powerEgg(),
              $fmt->asInteger(TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'max_power'))),
            ]),
            ['class' => 'nobr mr-3'],
          ),
        ]),
      ],
      [
        'label' => vsprintf('%s (%s)', [
          Yii::t('app', 'Average'),
          Yii::t('app-salmon3', 'Team Total'),
        ]),
        'format' => 'raw',
        'value' => implode('', [
          Html::tag(
            'span',
            implode(' ', [
              Icon::goldenEgg(),
              $fmt->asDecimal(TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'avg_golden')), 1),
              Html::tag(
                'small',
                sprintf('(σ=%s)', $fmt->asDecimal(TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'sd_golden')), 1)),
                ['class' => 'auto-tooltip text-muted', 'title' => Yii::t('app', 'Standard Deviation')],
              ),
            ]),
            ['class' => 'nobr mr-3'],
          ),
          Html::tag(
            'span',
            implode(' ', [
              Icon::powerEgg(),
              $fmt->asDecimal(TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'avg_power')), 0),
              Html::tag(
                'small',
                sprintf('(σ=%s)', $fmt->asDecimal(TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'sd_power')), 0)),
                ['class' => 'auto-tooltip text-muted', 'title' => Yii::t('app', 'Standard Deviation')],
              ),
            ]),
            ['class' => 'nobr mr-3'],
          ),
        ]),
      ],
      [
        'label' => vsprintf('%s (%s)', [
          Yii::t('app', 'Maximum'),
          Yii::t('app-salmon3', 'Personal'),
        ]),
        'format' => 'raw',
        'value' => implode('', [
          Html::tag(
            'span',
            implode(' ', [
              Icon::goldenEgg(),
              $fmt->asInteger(TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'max_golden_individual'))),
            ]),
            ['class' => 'nobr mr-3'],
          ),
          Html::tag(
            'span',
            implode(' ', [
              Icon::powerEgg(),
              $fmt->asInteger(TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'max_power_individual'))),
            ]),
            ['class' => 'nobr mr-3'],
          ),
        ]),
      ],
      [
        'label' => vsprintf('%s (%s)', [
          Yii::t('app', 'Average'),
          Yii::t('app-salmon3', 'Personal'),
        ]),
        'format' => 'raw',
        'value' => implode('', [
          Html::tag(
            'span',
            implode(' ', [
              Icon::goldenEgg(),
              $fmt->asDecimal(TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'avg_golden_individual')), 1),
              Html::tag(
                'small',
                sprintf('(σ=%s)', $fmt->asDecimal(TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'sd_golden_individual')), 1)),
                ['class' => 'auto-tooltip text-muted', 'title' => Yii::t('app', 'Standard Deviation')],
              ),
            ]),
            ['class' => 'nobr mr-3'],
          ),
          Html::tag(
            'span',
            implode(' ', [
              Icon::powerEgg(),
              $fmt->asDecimal(TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'avg_power_individual')), 0),
              Html::tag(
                'small',
                sprintf('(σ=%s)', $fmt->asDecimal(TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'sd_power_individual')), 0)),
                ['class' => 'auto-tooltip text-muted', 'title' => Yii::t('app', 'Standard Deviation')],
              ),
            ]),
            ['class' => 'nobr mr-3'],
          ),
        ]),
      ],
      isset($overfishing) && $overfishing
        ? [
          'label' => Yii::t('app-salmon-overfishing', 'Overfishing'),
          'format' => 'raw',
          'value' => implode('', [
            Html::button(
              implode(' ', [
                Icon::goldenEgg(),
                Html::encode(Yii::t('app-salmon-overfishing', 'Overfishing Stats')),
              ]),
              [
                'class' => 'btn btn-default btn-xs',
                'data' => [
                  'toggle' => 'modal',
                  'target' => '#overfishing-stats',
                ],
              ],
            ),
            $this->render('overfishing', [
              'events' => $events,
              'modalId' => 'overfishing-stats',
              'stats' => $overfishing,
              'tides' => $tides,
            ]),
          ]),
        ]
        : null,
      [
        'label' => Yii::t('app-salmon3', 'Scales'),
        'format' => 'raw',
        'value' => implode('', [
          Html::tag(
            'span',
            implode(' ', [
              Icon::goldScale(),
              $fmt->asInteger((int)TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'total_gold_scale'))),
            ]),
            ['class' => 'nobr mr-3'],
          ),
          Html::tag(
            'span',
            implode(' ', [
              Icon::silverScale(),
              $fmt->asInteger((int)TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'total_silver_scale'))),
            ]),
            ['class' => 'nobr mr-3'],
          ),
          Html::tag(
            'span',
            implode(' ', [
              Icon::bronzeScale(),
              $fmt->asInteger((int)TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'total_bronze_scale'))),
            ]),
            ['class' => 'nobr mr-3'],
          ),
        ]),
      ],
      [
        'label' => Icon::s3Rescues() . ' ' . Yii::t('app-salmon3', 'Rescues'),
        'format' => 'raw',
        'value' => $totalAndAvg('total_rescues', 'avg_rescues', 'sd_rescues',),
      ],
      [
        'label' => Icon::s3Rescued() . ' ' . Yii::t('app-salmon3', 'Rescued'),
        'format' => 'raw',
        'value' => $totalAndAvg('total_rescued', 'avg_rescued', 'sd_rescued'),
      ],
      [
        'label' => Yii::t('app-salmon2', 'Boss Salmonids'),
        'format' => 'raw',
        'value' => $totalAndAvg('total_defeat_boss', 'avg_defeat_boss', 'sd_defeat_boss'),
      ],
    ],
    fn (?array $row): bool => $row !== null && $row['value'] !== null,
  ),
]);
