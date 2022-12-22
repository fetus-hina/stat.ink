<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
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

$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

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
      vsprintf('%s %s', [
        Html::img(
          $am->getAssetUrl(
            $am->getBundle(GameModeIconsAsset::class),
            sprintf('spl3/%s.png', $rule->key),
          ),
          [
            'class' => 'basic-icon',
            'draggable' => 'false',
          ],
        ),
        Html::encode(Yii::t('app-rule3', $rule->name)),
      ]),
      [
        'class' => 'omit',
        'style' => $cellStyle,
      ],
    ) . "\n" ?>
<?php } ?>
  </tr>
</thead>
