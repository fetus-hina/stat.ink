<?php

declare(strict_types=1);

use app\components\helpers\StandardError;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, array{battles: int, wins: int}> $data
 */

return [
  'data' => array_filter(
    array_map(
      function (int $uses, array $row): ?array {
        if (!$err = StandardError::winpct($row['wins'], $row['battles'])) {
          return null;
        }

        return [
          'x' => $uses,
          // 'y' => 100.0 * $err['rate'],
          'yMax' => [100.0 * $err['max95ci'], 100.0 * $err['max99ci']],
          'yMin' => [100.0 * $err['min95ci'], 100.0 * $err['min99ci']],
        ];
      },
      array_keys($data),
      array_values($data),
    ),
    fn (?array $v): bool => $v !== null,
  ),
  'errorBarColor' => 'rgba(50, 50, 50, 0.75)',
  'errorBarLineWidth' => 1,
  'errorBarWhiskerColor' => 'rgba(50, 50, 50, 0.75)',
  'errorBarWhiskerLineWidth' => 1,
  'label' => Yii::t('app', 'Win %'),
  'type' => 'scatterWithErrorBars',
];
