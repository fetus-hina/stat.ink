<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;
use app\models\Medal3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\AssetManager;

return [
  'contentOptions' => ['class' => 'cell-medal'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-medal'],
  'label' => Yii::t('app', 'Medals'),
  'value' => function (Battle3 $model): ?string {
    $medals = ArrayHelper::sort(
      $model->medals,
      function (Medal3 $a, Medal3 $b): int {
        $ca = $a->canonical;
        $cb = $b->canonical;
        if ((bool)$ca !== (bool)$cb) { // どちらかが canonical データがない
          return $ca ? -1 : 1; // canonical データを持つ方が先
        }

        if ($ca && $cb) {
          if ($ca->gold !== $cb->gold) {
            return $ca->gold ? -1 : 1;
          }

          return strcmp($ca->name, $cb->name);
        }

        return strcmp($a->name, $b->name);
      },
    );
    if (!$medals) {
      return null;
    }

    $am = Yii::$app->assetManager;
    assert($am instanceof AssetManager);

    $items = [];
    foreach ($medals as $medal) {
      $canonical = $medal->canonical;
      if ($canonical) {
        $items[] = Html::tag(
          'span',
          $canonical->gold ? Icon::goldMedal() : Icon::silverMedal(),
          [
            'class' => 'auto-tooltip text-muted',
            'title' => Yii::t('app-medal3', $canonical->name),
          ],
        );
      } else {
        $items[] = Html::tag(
          'span',
          Icon::unknown(),
          [
            'class' => 'auto-tooltip text-muted',
            'title' => $medal->name,
          ],
        );
      }
    }

    return implode(' ', $items);
  },
];
