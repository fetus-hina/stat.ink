<?php

declare(strict_types=1);

use app\components\helpers\Html;
use app\models\Salmon2;
use app\models\User;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Salmon2|null $next
 * @var Salmon2|null $prev
 * @var User $user
 * @var View $this
 */

if (!$prev && !$next) {
  return;
}

if ($prev) {
  $this->registerLinkTag([
    'rel' => 'prev',
    'href' => Url::to(
      ['salmon/view', 'screen_name' => $user->screen_name, 'id' => $prev->id],
      true
    ),
  ]);
}

if ($next) {
  $this->registerLinkTag([
    'rel' => 'next',
    'href' => Url::to(
      ['salmon/view', 'screen_name' => $user->screen_name, 'id' => $next->id],
      true
    ),
  ]);
}
?>
<div class="row" style="margin-bottom:15px">
  <div class="col-xs-6 text-left">
<?php if ($prev) { ?>
    <?= Html::a(
      '<span class="fas fa-fw fa-angle-double-left"></span>' . Yii::t('app-salmon2', 'Prev. Job'),
      ['salmon/view', 'screen_name' => $user->screen_name, 'id' => $prev->id],
      ['class' => 'btn btn-default']
    ) . "\n" ?>
<?php } else { ?>
    &nbsp;
<?php } ?>
  </div>
  <div class="col-xs-6 text-right">
<?php if ($next) { ?>
    <?= Html::a(
      Yii::t('app-salmon2', 'Next Job') . '<span class="fas fa-fw fa-angle-double-right"></span>',
      ['salmon/view', 'screen_name' => $user->screen_name, 'id' => $next->id],
      ['class' => 'btn btn-default']
    ) . "\n" ?>
<?php } else { ?>
    &nbsp;
<?php } ?>
  </div>
</div>
