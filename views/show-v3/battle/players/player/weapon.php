<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
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
    Icon::s3Weapon($weapon),
    Html::encode(Yii::t('app-weapon3', $weapon->name)),
    Icon::s3Subweapon($weapon->subweapon),
    Icon::s3Special($weapon->special),
  ]) . "\n" ?>
<?php } ?>
</div>
