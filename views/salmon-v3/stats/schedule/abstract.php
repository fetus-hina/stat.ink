<?php

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Map3;
use app\models\Salmon3;
use app\models\SalmonSchedule3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var Map3|SalmonMap3|null $map
 * @var SalmonSchedule3 $schedule
 * @var User $user
 * @var View $this
 * @var array<string, scalar|null> $stats
 */

$fmt = Yii::$app->formatter;

$totalAndAvg = fn (string $attrTotal, string $attrAvg): string => sprintf(
  '%s (%s)',
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
    $fmt->asInteger(TypeHelper::intOrNull(ArrayHelper::getValue($stats, $attrTotal))) ?: '-',
    [
      'class' => 'auto-tooltip',
      'title' => Yii::t('app', 'Total'),
    ],
  ),
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
  'attributes' => [
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
                $schedule->big_map_id !== null => 'bigrun',
                $schedule->is_eggstra_work => 'eggstra',
                default => 'normal',
              },
              'term' => 'term',
              'term_from' => sprintf('@%d', strtotime($schedule->start_at)),
              'term_to' => sprintf('@%d', strtotime($schedule->end_at)),
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
      'value' => match ($v = TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'king_appears'))) {
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
    ],
    [
      'label' => Yii::t('app-salmon3', 'Max. Hazard Level (cleared)'),
      'value' => match ($v = TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'max_danger_rate'))) {
        null => '-',
        default => $fmt->asPercent(((int)$v) / 100, 0),
      },
    ],
    [
      'label' => Yii::t('app', 'Maximum'),
      'format' => 'raw',
      'value' => implode('', [
        Html::tag(
          'span',
          vsprintf('%s %s', [
            Html::tag('span', Icon::goldenEgg(), [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app-salmon2', 'Golden Eggs'),
            ]),
            $fmt->asInteger(TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'max_golden'))),
          ]),
          ['class' => 'nobr mr-3'],
        ),
        Html::tag(
          'span',
          vsprintf('%s %s', [
            Html::tag('span', Icon::powerEgg(), [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app-salmon2', 'Power Eggs'),
            ]),
            $fmt->asInteger(TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'max_power'))),
          ]),
          ['class' => 'nobr mr-3'],
        ),
      ]),
    ],
    [
      'label' => Yii::t('app-salmon3', 'Fish Scales'),
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
      'label' => Yii::t('app-salmon3', 'Rescues'),
      'format' => 'raw',
      'value' => $totalAndAvg('total_rescues', 'avg_rescues'),
    ],
    [
      'label' => Yii::t('app-salmon3', 'Rescued'),
      'format' => 'raw',
      'value' => $totalAndAvg('total_rescued', 'avg_rescued'),
    ],
    [
      'label' => Yii::t('app-salmon2', 'Boss Salmonids'),
      'format' => 'raw',
      'value' => $totalAndAvg('total_defeat_boss', 'avg_defeat_boss'),
    ],
  ],
]);
