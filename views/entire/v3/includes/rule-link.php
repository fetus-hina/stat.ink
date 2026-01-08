<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\InlineListAsset;
use app\components\widgets\Icon;
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
        Html::a(
          trim(
            implode(' ', [
              Icon::s3Rule($rule),
              Html::encode(Yii::t('app-rule3', $rule->name)),
            ]),
          ),
          "#{$rule->key}",
        ),
      ),
      $rules,
    ),
  ),
  ['class' => 'inline-list mb-3'],
);
