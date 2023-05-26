<?php

declare(strict_types=1);

use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var View $this
 */

$am = Yii::$app->assetManager;

echo Html::tag(
  'h2',
  Html::encode(Yii::t('app-rule3', $rule->name)),
  ['id' => $rule->key],
);
