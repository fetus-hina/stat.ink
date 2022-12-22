<?php

declare(strict_types=1);

use app\assets\Salmon3WorkListConfigAsset;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

Salmon3WorkListConfigAsset::register($this);

?>
<div class="row">
  <div class="col-xs-12" id="table-config">
    <div>
      <label>
        <input type="checkbox" id="table-hscroll" value="1">
        <?= Html::encode(Yii::t('app', 'Always enable horizontal scroll')) . "\n" ?>
      <label>
    </div>
    <div class="row"><?= $this->render('filter/list', compact('user')) ?></div>
  </div>
</div>
