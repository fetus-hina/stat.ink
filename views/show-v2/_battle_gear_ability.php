<?php

declare(strict_types=1);

use app\components\helpers\Html;
use app\components\widgets\AbilityIcon;
use app\models\Ability2;
use yii\web\View;

/**
 * @var View $this
 * @var bool|null $lockedIfNull
 * @var ?Ability2 $ability
 */

$this->registerCss('.ability{width:auto;height:3.5em}.sub-ability .ability{height:1.667em}');

if (!$ability) {
  if (isset($lockedIfNull) && $lockedIfNull) {
    echo AbilityIcon::spl2('locked', [
      'title' => Yii::t('app-ability2', '(Locked)'),
      'class' => ['auto-tooltip'],
    ]);
  }
} else {
  echo AbilityIcon::spl2($ability->key, [
    'title' => Yii::t('app-ability2', $ability->name),
    'class' => ['auto-tooltip'],
  ]);
}
