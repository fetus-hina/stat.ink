<?php

declare(strict_types=1);

use app\assets\BattleListGroupHeaderAsset;
use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\Salmon3;
use app\models\SalmonSchedule3;
use app\models\SalmonScheduleWeapon3;
use app\models\User;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

BattleListGroupHeaderAsset::register($this);

return function (Salmon3 $model, int $key, int $index, GridView $widget) use ($user): ?string {
  static $lastScheduleId = false;
  static $isPrivate = null;
  if ($lastScheduleId === $model->schedule_id && $model->is_private === $isPrivate) {
    return null;
  }

  $lastScheduleId = $model->schedule_id;
  $isPrivate = $model->is_private;

  if ($lastScheduleId === null || $isPrivate) {
    return Html::tag(
      'tr',
      Html::tag(
        'td',
        $isPrivate
          ? Html::encode(Yii::t('app-salmon3', 'Private Job'))
          : Html::encode(Yii::t('app', 'Unknown')),
        [
          'class' => 'battle-row-group-header',
          'colspan' => (string)count($widget->columns),
        ]
      ),
    );
  }

  $schedule = $model->schedule;
  if (!$schedule instanceof SalmonSchedule3) {
    // Logic Error
    return null;
  }

  $weapons = implode(
    '',
    ArrayHelper::getColumn(
      SalmonScheduleWeapon3::find()
        ->with(['random', 'weapon'])
        ->andWhere(['schedule_id' => $schedule->id])
        ->orderBy(['id' => SORT_ASC])
        ->all(),
      fn (SalmonScheduleWeapon3 $weaponInfo): string => Html::tag(
        'span',
        WeaponIcon::widget([
          'model' => $weaponInfo->weapon ?? $weaponInfo->random ?? null,
        ]),
        ['class' => 'mr-1'],
      ),
    ),
  );

  $dateTimes = vsprintf('%s - %s', [
    $widget->formatter->asHtmlDatetimeEx($schedule->start_at, 'medium', 'short'),
    $widget->formatter->asHtmlDatetimeEx($schedule->end_at, 'medium', 'short'),
  ]);

  return Html::tag(
    'tr',
    Html::tag(
      'td',
      vsprintf('%s %s', [
        $weapons,
        $dateTimes,
      ]),
      [
        'class' => 'battle-row-group-header',
        'colspan' => (string)count($widget->columns),
      ],
    ),
  );
};
