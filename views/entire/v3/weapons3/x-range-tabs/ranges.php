<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\StatWeapon3XUsageRange;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatWeapon3XUsageRange[] $xRanges
 * @var StatWeapon3XUsageRange|null $xRange
 * @var View $this
 * @var callable(StatWeapon3XUsageRange|null): string $xRangeUrl
 */

// '[2000.0,)' => [(float)2000.0, null]|null
$extractRange = function (string $value): ?array {
  $parts = explode(',', trim($value, '[]()'), 2);
  return count($parts) === 2
    ? array_map(TypeHelper::floatOrNull(...), $parts)
    : null;
};

foreach ($xRanges as $model) {
  $range = $extractRange($model->x_power_range);

  echo Html::tag(
    'li',
    Html::tag(
      'a',
      trim(
        implode(' ', [
          Icon::s3LobbyX(),
          Html::encode(Yii::t('app', 'XP')),
          $range
            ? Html::encode(
              trim(
                Yii::t('app', '{from} - {to}', [
                  'from' => $range[0] ? Yii::$app->formatter->asDecimal($range[0], 0) : '',
                  'to' => $range[1] ? Yii::$app->formatter->asDecimal($range[1], 0) : '',
                ]),
              ),
            )
            : Html::encode($model->x_power_range),
        ]),
      ),
      $xRange?->id === $model->id ? [] : ['href' => $xRangeUrl($model)],
    ),
    [
      'role' => 'presentation',
      'class' => $xRange?->id === $model->id ? 'active' : false,
    ],
  );
}
