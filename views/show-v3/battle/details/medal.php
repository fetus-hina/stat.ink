<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;
use app\models\BattleMedal3;
use yii\helpers\Html;
use yii\web\AssetManager;

return [
  'label' => Yii::t('app-show-v3', 'Medals'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    $intermediates = BattleMedal3::find()
      ->with(['medal', 'medal.canonical'])
      ->andWhere(['battle_id' => $model->id])
      ->orderBy(['id' => SORT_ASC])
      ->all();

    if (!$intermediates) {
      return null;
    }

    $items = [];
    foreach ($intermediates as $i => $intermediate) {
      $medal = $intermediate->medal;
      $canonical = $medal->canonical;

      $items[] = Html::tag(
        'div',
        implode(' ', [
          $canonical
            ? ($canonical->gold ? Icon::s3GoldMedal() : Icon::s3SilverMedal())
            : '',
          Html::encode(
            $canonical
              ? Yii::t('app-medal3', $canonical->name)
              : $medal->name,
          ),
        ]),
        [
          'class' => count($intermediates) - 1 !== $i // is not last?
            ? 'mb-1'
            : '',
        ],
      );
    }

    return implode('', $items);
  },
];
