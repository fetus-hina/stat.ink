<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\StandardError;
use app\models\Lobby3;
use app\models\Splatfest3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var View $this
 * @var array<string, int> $dragons
 * @var array{lobby_id: int, fest_dragon_id: int|null, battles: int}[] $dragonStats
 */

$stats = array_values(
  array_filter(
    $dragonStats,
    fn (array $row) => (int)$row['lobby_id'] === (int)$lobby->id,
  ),
);

$samples = array_sum(ArrayHelper::getColumn($stats, 'battles'));

$drawCell = function (int|false|null $dragonId) use ($samples, $stats): string {
  $row = $dragonId === null || is_int($dragonId)
    ? array_filter(
      $stats,
      fn (array $row) => $row['fest_dragon_id'] === $dragonId,
    )
    : null;

  $battles = $row
    ? array_sum(ArrayHelper::getColumn($row, 'battles'))
    : null;

  $info = $row
    ? StandardError::winpct($battles, $samples)
    : null;

  return Html::tag(
    'td',
    $dragonId !== false && $samples > 0 && $row
      ? (
        $info
          ? Html::tag(
            'span',
            Html::encode(Yii::$app->formatter->asPercent($info['rate'], 2)),
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app', '{from} - {to}', [
                'from' => Yii::$app->formatter->asPercent($info['min95ci'], 2),
                'to' => Yii::$app->formatter->asPercent($info['max95ci'], 2),
              ]),
            ],
          )
          : Yii::$app->formatter->asPercent($battles / $samples, 2)
      )
      : '-',
    ['class' => 'text-center'],
  );
};

?>
<tr>
  <th scope="row"><?= Html::encode(Yii::t('app-lobby3', $lobby->name)) ?></th>
  <td class="text-center"><?= Yii::$app->formatter->asInteger($samples) ?></td>
  <?= $drawCell(null) . "\n" ?>
  <?= $drawCell($dragons['10x'] ?? false) . "\n" ?>
  <?= $drawCell($dragons['100x'] ?? false) . "\n" ?>
  <?= $drawCell($dragons['333x'] ?? false) . "\n" ?>
</tr>
