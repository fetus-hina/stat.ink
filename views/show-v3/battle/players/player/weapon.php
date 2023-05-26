<?php

declare(strict_types=1);

use app\models\Weapon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var ?Weapon3 $weapon
 * @var View $this
 */

?>
<div class="flex-grow-1">
<?php if ($weapon) { ?>
  <?= Html::tag(
    'span',
    Html::encode(Yii::t('app-weapon3', $weapon->name)),
    [
      'class' => 'auto-tooltip',
      'title' => vsprintf('%s / %s', [
        Yii::t('app-subweapon3', $weapon->subweapon?->name ?? '?'),
        Yii::t('app-special3', $weapon->special?->name ?? '?'),
      ]),
    ],
  ) . "\n" ?>
<?php } ?>
</div>
