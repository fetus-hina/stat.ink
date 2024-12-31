<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, Rule3> $rules
 */

$cellWidth = sprintf('%f%%', 100.0 / (count($rules) + 1));
$cellStyle = [
  'min-width' => '200px',
  'width' => $cellWidth,
];

$am = TypeHelper::instanceOf(Yii::$app->assetManager, AssetManager::class);

?>
<thead>
  <tr>
    <?= Html::tag(
      'th',
      '',
      ['style' => $cellStyle],
    ) . "\n" ?>
<?php foreach ($rules as $rule) { ?>
    <?= Html::tag(
      'th',
      implode(' ', [
        Icon::s3Rule($rule),
        Html::encode(Yii::t('app-rule3', $rule->name)),
      ]),
      [
        'class' => 'omit text-center',
        'style' => $cellStyle,
      ],
    ) . "\n" ?>
<?php } ?>
  </tr>
</thead>
