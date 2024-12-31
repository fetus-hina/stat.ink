<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\StandardError;
use app\components\widgets\Icon;
use app\models\StatSalmon3Salmometer;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, StatSalmon3Salmometer> $data
 * @var int $totalCleared
 * @var int $totalSamples
 */

$fmt = Yii::$app->formatter;

if ($totalSamples < 1) {
  return;
}

$maxSamples = (int)max(ArrayHelper::getColumn($data, 'jobs'));

?>
<table class="table table-bordered table-striped m-0">
  <thead>
    <tr>
      <th class="text-center" colspan="2"></th>
      <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Clear %')) ?></th>
      <th class="text-center" colspan="2"><?= Html::encode(Yii::t('app', 'Samples')) ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th colspan="2" class="text-center">(<?= Html::encode(Yii::t('app', 'Total')) ?>)</th>
      <?= Html::tag(
        'td',
        $fmt->format(
          [$totalSamples, $totalCleared],
          function (array $data) use ($fmt): string {
            [$totalSamples, $totalCleared] = $data;
            if ($err = StandardError::winpct($totalCleared, $totalSamples)) {
              return Html::tag(
                'span',
                Html::encode($fmt->asPercent($err['rate'], 2)),
                [
                  'class' => 'auto-tooltip',
                  'title' => Yii::t('app', '{from} - {to}', [
                    'from' => $fmt->asPercent($err['min99ci'], 2),
                    'to' => $fmt->asPercent($err['max99ci'], 2),
                  ]),
                ],
              );
            }
            return Html::encode($fmt->asPercent($totalCleared / $totalSamples, 2));
          },
        ),
        ['class' => 'text-center'],
      ) . "\n" ?>
      <td class="text-center"><?= Html::encode($fmt->asInteger($totalSamples)) ?></td>
      <td class="text-center"></td>
    </tr>
    <?= implode(
      '',
      array_map(
        function (int $level) use ($data, $fmt, $maxSamples): string {
          $row = ArrayHelper::getValue($data, $level);
          return Html::tag(
            'tr',
            implode('', [
              Html::tag('td', Icon::s3Salmometer($level), ['class' => 'text-center']),
              Html::tag('td', Html::encode($fmt->asInteger($level)), ['class' => 'text-center']),
              Html::tag(
                'td',
                $fmt->format(
                  $row,
                  function (?StatSalmon3Salmometer $row) use ($fmt): string {
                    if (!$row || $row->jobs < 1) {
                      return '-';
                    }

                    if ($err = StandardError::winpct($row->cleared, $row->jobs)) {
                      return Html::tag(
                        'span',
                        Html::encode($fmt->asPercent($err['rate'], 2)),
                        [
                          'class' => 'auto-tooltip',
                          'title' => Yii::t('app', '{from} - {to}', [
                            'from' => $fmt->asPercent($err['min99ci'], 2),
                            'to' => $fmt->asPercent($err['max99ci'], 2),
                          ]),
                        ],
                      );
                    }

                    return Html::encode($fmt->asPercent($row->cleared / $row->jobs, 2));
                  },
                ),
                ['class' => 'text-center'],
              ),
              Html::tag(
                'td',
                Html::encode($fmt->asInteger($row?->jobs ?? 0)),
                ['class' => 'text-center'],
              ),
              Html::tag(
                'td',
                $row?->jobs > 0 && $maxSamples > 0
                  ? Progress::widget([
                    'percent' => $row->jobs / $maxSamples * 100,
                  ])
                  : '',
                [
                  'class' => 'text-center',
                  'style' => 'min-width:90px',
                ],
              ),
            ]),
          );
        },
        range(0, 5),
      ),
    ) . "\n" ?>
  </tbody>
</table>
