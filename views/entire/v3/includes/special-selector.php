<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Special3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Special3 $special
 * @var Special3[] $specials
 * @var View $this
 * @var callable(Special3): string $specialUrl
 */

?>
<?= Html::tag(
  'select',
  implode(
    '',
    ArrayHelper::getColumn(
      $specials,
      fn (Special3 $model): string => Html::tag(
        'option',
        Html::encode(Yii::t('app-special3', $model->name)),
        [
          'selected' => $model->key === $special->key,
          'value' => $specialUrl($model),
        ],
      ),
    ),
  ),
  [
    'class' => 'form-control mb-3',
    'onchange' => 'window.location.href = this.value',
  ],
);
