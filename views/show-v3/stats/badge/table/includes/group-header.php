<?php

declare(strict_types=1);

use app\assets\BattleListGroupHeaderAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $label
 */

BattleListGroupHeaderAsset::register($this);

?>
<tr>
  <td class="battle-row-group-header" colspan="3">
    <?= Html::encode($label) . "\n" ?>
  </td>
</tr>
