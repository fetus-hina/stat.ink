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
        $battles = (int)$row['battles'];
        if ($battles < 1) {
          return null;
        }
        $wins = (int)$row['wins'];

        return [
          'x' => $uses,
          'y' => 100.0 * $wins / $battles,
        ];
      },
      array_keys($data),
      array_values($data),
    ),
    fn (?array $v): bool => $v !== null,
  ),
  'label' => Yii::t('app', 'Win %'),
  'pointBackgroundColor' => '#f5a101',
  'pointRadius' => 4,
  'type' => 'scatter',
];
