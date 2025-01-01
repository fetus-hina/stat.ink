<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3|null $rule
 * @var View $this
 */

if (!$rule) {
  echo Html::tag('h3', Html::encode(Yii::t('app', 'Unknown')));
  return;
}

?>
<?= Html::tag(
  'h3',
  Html::tag(
    'span',
    Html::encode(Yii::t('app-rule3', $rule->short_name)),
    [
      'class' => 'auto-tooltip',
      'title' => Yii::t('app-rule3', $rule->name),
    ],
  ),
  [
    'class' => 'mt-0 mb-3 omit',
  ],
) ?>
