<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;
use yii\helpers\Html;

return [
  'label' => Yii::t('app', 'Specials'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->special === null) {
      return null;
    }

    $weapon = $model->weapon;
    if ($weapon) {
      $special = $weapon->special;
      if ($special) {
        return vsprintf('%s (%s)', [
          Html::encode(Yii::$app->formatter->asInteger($model->special)),
          Html::encode(Yii::t('app-special3', $special->name)),
        ]);
      }
    }

    return Html::encode(Yii::$app->formatter->asInteger($model->special));
  },
];
