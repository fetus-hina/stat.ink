<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\BattlePlayer3;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var BattlePlayer3 $player
 * @var View $this
 * @var bool $isFirst
 * @var int $nPlayers
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
  <td class="text-center"><?php
    if ($player->is_me) {
      echo Html::tag('span', '', [
        'class' => 'fas fa-fw fa-rotate-90 fa-level-up-alt',
      ]);
    }
  ?></td>
  <td><?php
    // TODO: blackout / anonymize
    $title = $player->splashtagTitle;
    if ($title || $player->number !== null) {
      echo Html::tag(
        'div',
        trim(vsprintf('%s %s', [
          Html::encode((string)$title->name),
          Html::encode($player->number !== null ? sprintf('#%04d', $player->number) : '')
        ])),
        ['class' => 'small text-muted']
      );
    }
    echo Html::tag('div', Html::encode($player->name));
  ?></td>
  <td><?php
    if ($player->weapon) {
      echo implode(' ', [
        WeaponIcon::widget(['model' => $player->weapon]),
        Html::encode(Yii::t('app-weapon3', $player->weapon->name)),
        SubweaponIcon::widget(['model' => $player->weapon->subweapon]),
        SpecialIcon::widget(['model' => $player->weapon->special]),
      ]);
    }
  ?></td>
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
    if ($player->weapon) {
      echo SpecialIcon::widget(['model' => $player->weapon->special]) . ' ';
    }
    echo $f->asInteger($player->special);
  ?></td>
</tr>
