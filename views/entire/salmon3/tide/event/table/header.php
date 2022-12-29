<?php

declare(strict_types=1);

use app\models\SalmonWaterLevel2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, SalmonWaterLevel2> $tides
 */

?>
<thead>
  <tr>
    <?= Html::tag(
      'th',
      '',
      [
        'class' => 'text-center',
        'rowspan' => 2,
      ],
    ) . "\n" ?>
<?php foreach ($tides as $tide) { ?>
    <?= Html::tag(
      'th',
      Html::encode(Yii::t('app-salmon-tide2', $tide->name)),
      [
        'class' => 'text-center',
        'colspan' => '2',
      ],
    ) . "\n" ?>
<?php } ?>
  </tr>
  <tr>
<?php foreach ($tides as $tide) { ?>
    <?= Html::tag(
      'th',
      Html::encode(Yii::t('app-salmon3', 'Occurrence %')),
      [
        'class' => 'text-center',
      ],
    ) . "\n" ?>
    <?= Html::tag(
      'th',
      Html::encode(Yii::t('app-salmon2', 'Clear %')),
      [
        'class' => 'text-center',
      ],
    ) . "\n" ?>
<?php } ?>
  </tr>
</thead>
