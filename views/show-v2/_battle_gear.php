<?php

declare(strict_types=1);

use app\models\GearConfiguration2;
use yii\helpers\Html;

$gears = [
    $headgear,
    $clothing,
    $shoes,
];
?>
<div class="table-responsive">
  <table class="table table-bordered table-condensed" style="margin-bottom:0">
    <thead>
      <tr>
        <th></th>
        <th><?= Html::encode(Yii::t('app-gear', 'Headgear')) ?></th>
        <th><?= Html::encode(Yii::t('app-gear', 'Clothing')) ?></th>
        <th><?= Html::encode(Yii::t('app-gear', 'Shoes')) ?></th>
      </tr>
    </thead>
    <tbody>
<?php if ($headgear->gear_id || $clothing->gear_id || $shoes->gear_id) { ?>
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
