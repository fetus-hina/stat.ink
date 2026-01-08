<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\TimestampColumnWidget;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Battle Start'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->start_at === null) {
      return null;
    }

    $dayFrom = (new DateTimeImmutable($model->start_at, new DateTimeZone(Yii::$app->timeZone)))
      ->setTime(0, 0, 0); // set to 00:00:00 (midnight)
    $dayTo = $dayFrom
      ->add(new DateInterval('P1D')) // move to next day's 00:00:00
      ->sub(new DateInterval('PT1S')); // back 1 second (23:59:59)

    return TimestampColumnWidget::widget([
      'value' => $model->start_at,
      'showRelative' => true,
      'formatter' => Yii::$app->formatter,
    ]);
  },
];
