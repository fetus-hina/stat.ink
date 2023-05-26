<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Ability3;
use app\models\BattlePlayer3;
use app\models\BattleTricolorPlayer3;
use app\models\XMatchingGroup3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BattlePlayer3|BattleTricolorPlayer3 $player
 * @var View $this
 * @var array<string, Ability3> $abilities
 * @var array<string, XMatchingGroup3> $weaponMatchingGroup
 * @var bool $isFirst
 * @var bool $isTricolor
 * @var bool $isXmatch
 * @var int $nPlayers
 * @var string|null $colorClass
 */

$f = Yii::$app->formatter;

$bgClass = null;
if ($player->is_me) {
  $bgClass = 'bg-success';
} elseif ($player->is_disconnected) {
  $bgClass = 'bg-danger';
}

?>
<?= Html::beginTag('tr', ['class' => $bgClass]) . "\n" ?>
  <?= Html::tag(
    'td',
    implode('', [
      $player->is_crowned
        ? Html::tag('div', Icon::crown(), ['class' => 'player-crown'])
        : '',
      $player->is_me
        ? Html::tag('div', Icon::thisPlayer())
        : '',
    ]),
    [
      'class' => array_filter([
        'text-center',
        // $colorClass, // crown が見えないケースがあるのでとりあえずやめ
      ]),
    ],
  ) . "\n" ?>
  <td><?= $this->render('player/name', compact('player')) ?></td>
  <?= Html::tag(
    'td',
    Html::tag(
      'div',
      implode('', [
        $this->render('player/weapon', ['weapon' => $player->weapon]),
        $this->render('player/abilities', compact('abilities', 'player')),
      ]),
      ['class' => 'h-100 d-flex flex-row flex-column'],
    ),
  ) . "\n" ?>
<?php if ($isXmatch) { ?>
  <?= $this->render('player/cell-x-matching-group', [
    'group' => $weaponMatchingGroup[$player->weapon?->key ?? 'unknown'] ?? null,
    'weapon' => $player->weapon,
  ]) . "\n" ?>
<?php } ?>
  <td class="text-right"><?= $f->asInteger($player->inked) ?></td>
  <td class="text-right"><?php
    if ($player->kill !== null) {
      if ($player->assist !== null) {
        echo vsprintf('%s %s', [
          $f->asInteger($player->kill),
          Html::tag(
            'small',
            vsprintf('+ %s', [
              $f->asInteger($player->assist),
            ]),
            ['class' => 'text-muted']
          ),
        ]);
      } else {
        echo $f->asInteger($player->kill);
      }
    } elseif ($player->kill_or_assist !== null) {
      echo vsprintf('≪%s≫', $f->asInteger($player->kill_or_assist));
    }
  ?></td>
  <td class="text-right"><?= $f->asInteger($player->death) ?></td>
  <td class="text-right"><?php
    if ($player->kill !== null && $player->death !== null) {
      if ($player->death > 0) {
        echo $f->asDecimal($player->kill / $player->death, 2);
      } elseif ($player->kill > 0) {
        echo $f->asDecimal(99.99, 2);
      } else {
        echo $f->asDecimal(1.0, 2);
      }
    }
  ?></td>
  <td class="text-right"><?php
    if ($player->special !== null) {
      echo Html::tag(
        'span',
        $f->asInteger($player->special),
        [
          'class' => 'auto-tooltip',
          'title' => Yii::t('app-special3', $player->weapon?->special?->name),
        ],
      );
    }
  ?></td>
<?php if ($isTricolor) { ?>
  <td class="text-right"><?php
    echo $f->asInteger(
      $player instanceof BattleTricolorPlayer3
        ? $player->signal
        : null,
    )
  ?></td>
<?php } ?>
</tr>
