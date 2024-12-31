<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3|null $rule
 * @var View $this
 * @var string|null $tag
 * @var string|true|null $id
 */

if (!$rule) {
  return;
}

if (($id ?? null) === true) {
  $id = $rule->key;
}

echo Html::tag(
  $tag ?? 'h2',
  implode(' ', [
    Icon::s3Rule($rule),
    Html::encode(Yii::t('app-rule3', $rule->name)),
  ]),
  [
    'class' => 'm-0 mb-3',
    'id' => $id ?? null,
  ],
);
