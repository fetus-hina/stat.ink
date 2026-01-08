<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Spl2GearAbilitiesSummaryWidget;
use app\models\Battle2;
use app\models\GearConfiguration2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle2 $battle
 * @var View $this
 */

$gears = [
  $battle->headgear,
  $battle->clothing,
  $battle->shoes,
];

?>
<div class="table-responsive">
  <table class="table table-bordered table-condensed m-0">
    <thead>
      <tr>
        <th></th>
        <th><?= Html::encode(Yii::t('app-gear', 'Headgear')) ?></th>
        <th><?= Html::encode(Yii::t('app-gear', 'Clothing')) ?></th>
        <th><?= Html::encode(Yii::t('app-gear', 'Shoes')) ?></th>
      </tr>
    </thead>
    <tbody>
<?php if ($battle->headgear->gear || $battle->clothing->gear || $battle->shoes->gear) { ?>
      <tr>
        <th scope="row"><?= Html::encode(Yii::t('app', 'Gear')) ?></th>
        <?= implode('', array_map(
          function (?GearConfiguration2 $gear): string {
            return Html::tag('td', Html::encode(Yii::t('app-gear2', $gear->gear->name ?? '?')));
          },
          $gears
        )) . "\n" ?>
      </tr>
<?php } ?>
      <tr>
        <th scope="row"><?= Html::encode(Yii::t('app', 'Primary Ability')) ?></th>
        <?= implode('', array_map(
          function (?GearConfiguration2 $gear): string {
            return Html::tag(
              'td',
              $this->render('_battle_gear_ability', [
                'ability' => $gear->primaryAbility ?? null,
                'lockedIfNull' => false,
              ])
            );
          },
          $gears
        )) . "\n" ?>
      </tr>
      <tr>
        <th scope="row">
          <?= Html::encode(Yii::t('app', 'Secondary Abilities')) . "\n" ?>
        </th>
        <?= implode('', array_map(
          function (?GearConfiguration2 $gear): string {
            return Html::tag(
              'td',
              implode(' ', array_map(
                function (int $i) use ($gear): string {
                  return (string)$this->render('_battle_gear_ability', [
                    'ability' => $gear->secondaries[$i]->ability ?? null,
                    'lockedIfNull' => count($gear->secondaries ?? []) >= $i,
                  ]);
                },
                range(0, 2)
              )),
              ['class' => 'sub-ability']
            );
          },
          $gears
        )) . "\n" ?>
      </tr>
    </tbody>
  </table>
</div>
<?= Spl2GearAbilitiesSummaryWidget::widget([
  'summary' => $battle->getGearAbilitySummary(),
]) . "\n" ?>
