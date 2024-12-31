<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;
use yii\helpers\Html;

return [
  'encodeLabel' => false,
  'label' => Html::tag('span', Html::encode(Yii::t('app', 'Ultra Signals')), [
    'title' => Yii::t('app', 'Try to secure the Ultra Signal'),
  ]),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->signal === null) {
      return null;
    }

    return Html::encode(Yii::$app->formatter->asInteger($model->signal));
  },
];
