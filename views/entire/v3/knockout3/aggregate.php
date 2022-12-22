<?php

declare(strict_types=1);

use app\models\Lobby3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $xMatch
 * @var View $this
 */

echo Html::tag(
  'p',
  Html::encode(
    Yii::t('app', 'Aggregated: {rules}', [
      'rules' => Yii::t('app-lobby3', $xMatch->name),
    ]),
  ),
  ['class' => 'mb-3'],
);
