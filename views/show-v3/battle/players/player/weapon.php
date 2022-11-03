<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\components\widgets\v3\weaponIcon\WeaponIcon;
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
  <?= implode(' ', [
    WeaponIcon::widget(['model' => $weapon]),
    Html::encode(Yii::t('app-weapon3', $weapon->name)),
    SubweaponIcon::widget(['model' => $weapon->subweapon]),
    SpecialIcon::widget(['model' => $weapon->special]),
  ]) . "\n" ?>
<?php } ?>
</div>
