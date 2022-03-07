<?php

declare(strict_types=1);

use app\components\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $data
 * @var string $modeKey
 * @var string $modeName
 */

echo Html::tag(
  'h2',
  Html::encode(Yii::t('app-rule2', $modeName)),
  ['id' => $modeKey]
) . "\n";

echo $this->render('//show-v2/user-stat-monthly-report/summarized-rules', ['data' => $data]) . "\n";
