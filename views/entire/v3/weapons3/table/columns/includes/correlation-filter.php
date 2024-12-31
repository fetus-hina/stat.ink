<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use MathPHP\Statistics\Correlation;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

return function (callable|string $columnGetter): callable {
  return function (Model $unused1, DataColumn $unused2, GridView $grid) use ($columnGetter): string {
    if (!$models = $grid->dataProvider->getModels()) {
      return '';
    }

    $data = [[], []];
    foreach ($models as $model) {
      if ($model->battles < 10) {
        continue;
      }

      $value = filter_var(ArrayHelper::getValue($model, $columnGetter), FILTER_VALIDATE_FLOAT);
      if (is_float($value)) {
        $data[0][] = $model->wins / $model->battles;
        $data[1][] = $value;
      }
    }
    if (count($data[0]) < 5 || count($data[1]) < 5) {
      return '';
    }

    $correlationCoefficient = Correlation::r($data[0], $data[1]);
    return Html::encode(Yii::$app->formatter->asDecimal($correlationCoefficient, 3));
  };
};
