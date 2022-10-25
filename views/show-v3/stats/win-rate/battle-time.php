<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $battles
 * @var int $seconds
 */

$f = Yii::$app->formatter;

if ($battles > 0) {
  $average = (int)round($seconds / $battles);
  echo Html::tag(
    'p',
    Yii::t('app', 'Avg. game in {time}', [
      'time' => vsprintf('%d:%02d', [
        (int)floor($average / 60),
        $average % 60,
      ]),
    ]),
    [
      'class' => 'small mt-2 mb-0 omit',
    ]
  );
}
