<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon2;
use app\models\User;
use yii\helpers\Html;
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
