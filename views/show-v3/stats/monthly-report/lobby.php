<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Lobby3;
use app\models\Spl3YearMonthForm;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var Spl3YearMonthForm $form
 * @var User $user
 * @var View $this
 * @var list<array<string, mixed>> $rules
 */

// Columns: 2 (xs - md), 4 (lg)
$clearfix = fn (int $count): string => implode('', array_filter([
  $count % 2 === 0 ? Html::tag('div', '', ['class' => 'clearfix visible-xs-block visible-sm-block visible-md-block']) : null,
  $count % 4 === 0 ? Html::tag('div', '', ['class' => 'clearfix visible-lg-block']) : null,
]));

?>
<?= Html::tag(
  'h3',
  implode(' ', array_filter([
    Icon::s3Lobby($lobby),
    Html::encode(Yii::t('app-lobby3', $lobby->name)),
  ])),
  [
    'class' => 'mt-0 mb-3',
    'id' => 'lobby-' . $lobby->key,
  ],
) . "\n" ?>
<div class="row">
<?php foreach ($rules as $i => $ruleInfo) { ?>
  <div class="col-xs-6 col-lg-3 mb-3">
    <?= $this->render('rule', [
      'form' => $form,
      'lobby' => $lobby,
      'maps' => $ruleInfo['maps'],
      'rule' => $ruleInfo['rule'],
      'stats' => $ruleInfo['stats'],
      'user' => $user,
    ]) . "\n" ?>
  </div>
  <?= $clearfix($i + 1) . "\n" ?>
<?php } ?>
</div>
