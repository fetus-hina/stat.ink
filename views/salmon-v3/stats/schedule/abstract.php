<?php

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Map3;
use app\models\Salmon3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use app\models\SalmonSchedule3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var Map3|SalmonMap3|null $map
 * @var SalmonKing3|null $king
 * @var SalmonSchedule3 $schedule
 * @var User $user
 * @var View $this
 * @var array<string, scalar|null> $stats
 */

$fmt = Yii::$app->formatter;

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
              'lobby' => $schedule->is_eggstra_work ? 'eggstra' : 'normal',
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
        default => vsprintf('%s (%s / %s) [%s]', [
          $fmt->asPercent(
            TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'king_defeated')) / $v,
            1,
          ),
          $fmt->asInteger((int)TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'king_defeated'))),
          $fmt->asInteger($v),
          $king ? Yii::t('app-salmon-boss3', $king->name) : '???',
        ]),
      },
    ],
    [
      'label' => Icon::goldenEgg() . ' ' . Html::encode(Yii::t('app', 'Maximum')),
      'format' => 'integer',
      'attribute' => 'max_golden',
    ],
    [
      'label' => Icon::powerEgg() . ' ' . Html::encode(Yii::t('app', 'Maximum')),
      'format' => 'integer',
      'attribute' => 'max_power',
    ],
    [
      'label' => Yii::t('app-salmon3', 'Max. Hazard Level (cleared)'),
      'value' => match ($v = TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'max_danger_rate'))) {
        null => '-',
        default => $fmt->asPercent(((int)$v) / 100, 0),
      },
    ],
    [
      'label' => Yii::t('app-salmon3', 'Rescues'),
      'attribute' => 'avg_rescues',
      'format' => ['decimal', 2],
    ],
    [
      'label' => Yii::t('app-salmon3', 'Rescued'),
      'attribute' => 'avg_rescued',
      'format' => ['decimal', 2],
    ],
    [
      'label' => Yii::t('app-salmon3', 'Boss Salmonid'),
      'attribute' => 'avg_defeat_boss',
      'format' => ['decimal', 2],
    ],
  ],
]);
