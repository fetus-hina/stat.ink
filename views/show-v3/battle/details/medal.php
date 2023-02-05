<?php

declare(strict_types=1);

use app\assets\Medal3Asset;
use app\models\Battle3;
use app\models\BattleMedal3;
use app\models\MedalCanoical3;
use yii\helpers\Html;
use yii\web\AssetManager;

return [
  'label' => Yii::t('app', 'Medals'),
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

    $am = Yii::$app->assetManager;
    assert($am instanceof AssetManager);

    $items = [];
    foreach ($intermediates as $i => $intermediate) {
      /**
       * @var MedalCanoical3|null $canonical
       */
      $canonical = null;
      if ($model = $intermediate->medal) {
        $canonical = $model->canonical;
      }

      $items[] = Html::tag(
        'div',
        implode(' ', [
          $canonical
            ? Html::img(
              $am->getAssetUrl(
                $am->getBundle(Medal3Asset::class),
                $canonical->gold ? 'gold.png' : 'silver.png',
              ),
              ['class' => 'basic-icon'],
            )
            : '',
          Html::encode(
            $canonical
              ? Yii::t('app-medal3', $canonical->name)
              : $model->name,
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
