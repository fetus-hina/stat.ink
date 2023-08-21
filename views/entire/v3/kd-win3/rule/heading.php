<?php

declare(strict_types=1);

use app\components\widgets\Icon;
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
  implode(' ', [
    match ($rule->key) {
      'area' => Icon::s3RuleArea(),
      'asari' => Icon::s3RuleAsari(),
      'hoko' => Icon::s3RuleHoko(),
      'nawabari' => Icon::s3RuleNawabari(),
      'yagura' => Icon::s3RuleYagura(),
      default => '',
    },
    Html::encode(Yii::t('app-rule3', $rule->name)),
  ]),
  ['id' => $rule->key],
);
