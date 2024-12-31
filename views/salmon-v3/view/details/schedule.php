<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\components\widgets\v3\BigrunPercentile;
use app\models\Salmon3;
use app\models\SalmonScheduleWeapon3;
use app\models\UserStatBigrun3;
use app\models\UserStatEggstraWork3;
use yii\helpers\Html;

return [
  'label' => Yii::t('app-salmon2', 'Rotation'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if ($model->is_private) {
      return Html::encode(
        Yii::t('app-salmon3', 'Private Job'),
      );
    }

    $schedule = $model->schedule;
    if (!$schedule) {
      return null;
    }

    $weapons = SalmonScheduleWeapon3::find()
      ->with(['weapon', 'random'])
      ->andWhere(['schedule_id' => $schedule->id])
      ->orderBy(['id' => SORT_ASC])
      ->all();
    if (!$weapons) {
      return null;
    }

    $parts = [];
    $parts[] = Html::a(
      Icon::search(),
      ['salmon-v3/index',
        'screen_name' => $model->user?->screen_name,
        'f' => [
          'lobby' => match (true) {
            $model->is_private => 'private',
            $model->is_eggstra_work => 'eggstra',
            $model->is_big_run => 'bigrun',
            default => 'normal',
          },
          'term' => 'term',
          'term_from' => sprintf('@%d', (int)strtotime($schedule->start_at)),
          'term_to' => sprintf('@%d', (int)strtotime($schedule->end_at) - 1),
        ],
      ],
    );

    if ($model->is_eggstra_work) {
      $parts[] = Icon::s3Eggstra();
    }

    if ($schedule->big_map_id) {
      $parts[] = Icon::s3BigRun();
    }

    if ($schedule->is_eggstra_work) {
      $eggstraStats = UserStatEggstraWork3::find()
        ->andWhere([
           'user_id' => $model->user_id,
           'schedule_id' => $schedule->id,
        ])
        ->limit(1)
        ->one();
      if ($eggstraStats && $eggstraStats->golden_eggs > 0) {
        $parts[] = vsprintf('%s %s', [
          Icon::goldenEgg(),
          Yii::$app->formatter->asInteger($eggstraStats->golden_eggs),
        ]);

        $parts[] = BigrunPercentile::widget([
          'schedule' => $schedule,
        ]);
      }
    }

    if ($schedule->big_map_id) {
      $bigrunStats = UserStatBigrun3::find()
        ->andWhere([
           'user_id' => $model->user_id,
           'schedule_id' => $schedule->id,
        ])
        ->limit(1)
        ->one();
      if ($bigrunStats && $bigrunStats->golden_eggs > 0) {
        $parts[] = implode(' ', [
          Icon::goldenEgg(),
          Yii::$app->formatter->asInteger($bigrunStats->golden_eggs),
        ]);

        $parts[] = BigrunPercentile::widget([
          'schedule' => $schedule,
        ]);
      }
    }

    $parts[] = Html::a(
      implode(' ', [
        Icon::stats(),
        Html::encode(Yii::t('app-salmon3', 'Per-Rotation Stats')),
      ]),
      ['salmon-v3/stats-schedule',
        'screen_name' => $model->user?->screen_name,
        'schedule' => $schedule->id,
      ],
      ['class' => 'btn btn-default btn-xs p-1'],
    );

    return implode(' ', $parts);
  },
];
