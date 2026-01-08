<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AbilityIcon;
use app\models\Ability2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Ability2|null $ability
 * @var View $this
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
