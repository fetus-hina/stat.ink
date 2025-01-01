<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
    Icon::s3Rule($rule),
    Html::encode(Yii::t('app-rule3', $rule->name)),
  ]),
  ['id' => $rule->key],
);
