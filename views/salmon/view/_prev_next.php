<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon2;
use yii\helpers\Html;
use yii\helpers\Url;

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
      implode(' ', [
        Icon::prevPage(),
        Yii::t('app-salmon2', 'Prev. Job'),
      ]),
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
      implode(' ', [
        Yii::t('app-salmon2', 'Next Job'),
        Icon::nextPage(),
      ]),
      ['salmon/view', 'screen_name' => $user->screen_name, 'id' => $next->id],
      ['class' => 'btn btn-default']
    ) . "\n" ?>
<?php } else { ?>
    &nbsp;
<?php } ?>
  </div>
</div>
