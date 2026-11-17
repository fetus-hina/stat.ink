<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\assets\FlexboxAsset;
use app\components\widgets\Icon;
use app\models\Lobby3;
use app\models\LobbyGroup3;
use app\models\Spl3YearMonthForm;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Spl3YearMonthForm $form
 * @var User $user
 * @var View $this
 * @var list<array{
 *   group: LobbyGroup3,
 *   firstLobby: Lobby3|null,
 *   stats: array<string, int|bool>|null
 * }> $groups
 */

FlexboxAsset::register($this);

// Columns: 2 (xs, sm), 3 (md), 6 (lg)
$clearfix = fn (int $count): string => implode('', array_filter([
  $count % 2 === 0 ? Html::tag('div', '', ['class' => 'clearfix visible-xs-block visible-sm-block']) : null,
  $count % 3 === 0 ? Html::tag('div', '', ['class' => 'clearfix visible-md-block']) : null,
  $count % 6 === 0 ? Html::tag('div', '', ['class' => 'clearfix visible-lg-block']) : null,
]));

?>
<div class="row">
<?php foreach ($groups as $i => $info) { ?>
  <div class="col-xs-6 col-md-4 col-lg-2 mb-3">
    <?= Html::tag(
      'h3',
      implode('', array_filter([
        // Only the name is truncated, so that the scroll link is always visible
        Html::tag(
          'span',
          implode(' ', array_filter([
            Icon::s3Lobby($info['group']),
            Html::encode(Yii::t('app-lobby3', $info['group']->name)),
          ])),
          ['class' => 'flex-grow-1 omit'],
        ),
        $info['firstLobby']
          ? Html::a(Icon::scrollTo(), '#lobby-' . $info['firstLobby']->key, ['class' => 'flex-shrink-0 ml-1'])
          : null,
      ])),
      ['class' => 'd-flex align-items-center mt-0 mb-2'],
    ) . "\n" ?>
<?php if ($info['stats']) { ?>
    <?= $this->render('win-pie', [
      'filter' => ['lobby' => '@' . $info['group']->key],
      'form' => $form,
      'stats' => $info['stats'],
      'user' => $user,
    ]) . "\n" ?>
<?php } else { ?>
    <p class="text-muted m-0"><?= Html::encode(Yii::t('app', 'No Data')) ?></p>
<?php } ?>
  </div>
  <?= $clearfix($i + 1) . "\n" ?>
<?php } ?>
</div>
