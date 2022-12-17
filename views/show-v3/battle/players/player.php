<?php

declare(strict_types=1);

use app\components\widgets\FA;
use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\models\Ability3;
use app\models\BattlePlayer3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BattlePlayer3 $player
 * @var View $this
 * @var array<string, Ability3> $abilities
 * @var bool $isFirst
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

if ($player->is_crowned) {
  $this->registerCss(
    vsprintf('.player-crown{%s}', [
      Html::cssStyleFromArray([
        'color' => '#f41',
        'text-shadow' => '1px 1px 0 #3336',
      ]),
    ]),
  );
}

?>
<?= Html::beginTag('tr', ['class' => $bgClass]) . "\n" ?>
  <?= Html::tag(
    'td',
    implode('', [
      $player->is_crowned
        ? Html::tag(
          'div',
          (string)FA::fas('crown')->fw(),
          ['class' => 'player-crown'],
        )
        : '',
      $player->is_me
        ? Html::tag(
          'div',
          (string)FA::fas('level-up-alt')->fw()->rotate(90),
        )
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
      if ($player->weapon) {
        echo SpecialIcon::widget(['model' => $player->weapon->special]) . ' ';
      }
      echo $f->asInteger($player->special);
    }
  ?></td>
</tr>
