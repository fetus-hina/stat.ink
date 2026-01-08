<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Weapon3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Weapon3 $weapon
 * @var Weapon3[] $weapons
 * @var View $this
 * @var callable(Weapon3): string $weaponUrl
 */

?>
<?= Html::tag(
  'select',
  implode(
    '',
    ArrayHelper::getColumn(
      $weapons,
      fn (Weapon3 $model): string => Html::tag(
        'option',
        Html::encode(Yii::t('app-weapon3', $model->name)),
        [
          'selected' => $model->key === $weapon->key,
          'value' => $weaponUrl($model),
        ],
      ),
    ),
  ),
  [
    'class' => 'form-control mb-1',
    'onchange' => 'window.location.href = this.value',
  ],
);
