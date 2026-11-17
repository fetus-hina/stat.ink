<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Spl3YearMonthForm;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Spl3YearMonthForm $form
 * @var User $user
 * @var View $this
 * @var string $route
 * @var bool|null $registerLinkTags
 */

$prev = $form->getPrevMonth();
$next = $form->getNextMonth();
if (!$prev && !$next) {
  return;
}

$makeUrl = fn (DateTimeInterface $date): string => Url::to(
  [
    $route,
    'screen_name' => $user->screen_name,
    'year' => (int)$date->format('Y'),
    'month' => (int)$date->format('n'),
  ],
  true,
);

if ($prev && ($registerLinkTags ?? false)) {
  $this->registerLinkTag(['rel' => 'prev', 'href' => $makeUrl($prev)]);
}

if ($next && ($registerLinkTags ?? false)) {
  $this->registerLinkTag(['rel' => 'next', 'href' => $makeUrl($next)]);
}

?>
<div class="row mb-3">
  <div class="col-xs-6">
<?php if ($prev) { ?>
    <?= Html::a(
      implode(' ', [
        Icon::prevPage(),
        Html::encode(Yii::t('app', 'Prev. Month')),
      ]),
      $makeUrl($prev),
      ['class' => 'btn btn-default'],
    ) . "\n" ?>
<?php } ?>
  </div>
  <div class="col-xs-6 text-right">
<?php if ($next) { ?>
    <?= Html::a(
      implode(' ', [
        Html::encode(Yii::t('app', 'Next Month')),
        Icon::nextPage(),
      ]),
      $makeUrl($next),
      ['class' => 'btn btn-default'],
    ) . "\n" ?>
<?php } ?>
  </div>
</div>
