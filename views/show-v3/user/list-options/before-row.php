<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\BattleListGroupHeaderAsset;
use app\components\helpers\Battle as BattleHelper;
use app\models\Battle3;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var View $this
 */

BattleListGroupHeaderAsset::register($this);

return function (Battle3 $model, int $key, int $index, GridView $widget): ?string {
  static $lastPeriod = null;
  if ($lastPeriod === $model->period) {
    return null;
  }

  $lastPeriod = $model->period;
  $fmt = Yii::$app->formatter;
  [$from, $to] = BattleHelper::periodToRange2DT($model->period);
  return Html::tag(
    'tr',
    Html::tag(
      'td',
      implode(' - ', [
        Html::tag(
          'time',
          Html::encode(
            implode(' ', array_filter([
              $fmt->asDate($from, 'medium'),
              $fmt->asTime($from, 'short'),
            ])),
          ),
          [
            'datetime' => $from->setTimezone(new DateTimeZone('Etc/UTC'))
              ->format(DateTime::ATOM),
          ],
        ),
        Html::tag(
          'time',
          Html::encode(
            implode(' ', array_filter([
              $fmt->asDate($from, 'medium') !== $fmt->asDate($to, 'medium')
                ? $fmt->asDate($to, 'medium')
                : null,
              $fmt->asTime($to, 'short'),
            ])),
          ),
          [
            'datetime' => $to->setTimezone(new DateTimeZone('Etc/UTC'))
              ->format(DateTime::ATOM),
          ],
        ),
      ]),
      [
        'class' => 'battle-row-group-header',
        'colspan' => (string)count($widget->columns),
      ]
    ),
  );
};
