<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon3;
use app\models\SalmonTitle3;
use yii\helpers\Html;

return [
  'label' => Yii::t('app', 'Title'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if (!$model->titleBefore && !$model->titleAfter) {
      return null;
    }

    $f = fn (?SalmonTitle3 $title, ?int $exp): string => ($title === null)
      ? Icon::unknown()
      : trim(
        implode(' ', [
          Html::encode(Yii::t('app-salmon-title3', $title->name)),
          $exp === null ? '' : Yii::$app->formatter->asInteger($exp),
        ]),
      );

    return implode(' ', [
      $f($model->titleBefore, $model->title_exp_before),
      Icon::arrowRight(),
      $f($model->titleAfter, $model->title_exp_after),
    ]);
  },
];
