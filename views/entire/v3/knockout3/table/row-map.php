<?php

declare(strict_types=1);

use app\assets\Spl3StageAsset;
use app\models\Knockout3;
use app\models\Map3;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Map3 $map
 * @var View $this
 * @var array<int, Knockout3> $data
 * @var array<int, Rule3> $rules
 */

$am = Yii::$app->assetManager;

?>
<tr>
  <?= Html::tag(
    'th',
    implode('<br>', [
      Html::encode(Yii::t('app-map3', $map->name)),
      Html::img(
        $am->getAssetUrl(
          $am->getBundle(Spl3StageAsset::class),
          sprintf('color-normal/%s.jpg', $map->key),
        ),
        ['class' => 'map-image'],
      ),
    ]),
    [
      'class' => 'text-center pb-3',
      'scope' => 'row',
    ],
  ) . "\n" ?>
<?php foreach ($rules as $ruleId => $rule) { ?>
  <?= $this->render('cell-pie', ['model' => $data[$ruleId] ?? null]) . "\n" ?>
<?php } ?>
</tr>
