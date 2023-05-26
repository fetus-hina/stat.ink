<?php

declare(strict_types=1);

use app\assets\InlineListAsset;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var Rule3 $rules
 * @var View $this
 */

InlineListAsset::register($this);

echo Html::tag(
  'ul',
  implode(
    '',
    array_map(
      fn (Rule3 $rule): string => Html::tag(
        'li',
        Html::tag(
          'a',
          Html::encode(Yii::t('app-rule3', $rule->name)),
          ['href' => "#{$rule->key}"],
        ),
      ),
      $rules,
    ),
  ),
  ['class' => 'inline-list mb-3'],
);
