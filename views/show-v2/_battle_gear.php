<?php
use yii\helpers\Html;
?>
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
      <td>
        <?= Html::encode(Yii::t('app-gear2', $headgear->gear->name ?? '?')) . "\n" ?>
      </td>
      <td>
        <?= Html::encode(Yii::t('app-gear2', $clothing->gear->name ?? '?')) . "\n" ?>
      </td>
      <td>
        <?= Html::encode(Yii::t('app-gear2', $shoes->gear->name ?? '?')) . "\n" ?>
      </td>
    </tr>
<?php } ?>
    <tr>
      <th scope="row"><?= Html::encode(Yii::t('app', 'Primary Ability')) ?></th>
      <td>
        <?= Html::encode(Yii::t('app-ability2', $headgear->primaryAbility->name ?? '?')) . "\n" ?>
      </td>
      <td>
        <?= Html::encode(Yii::t('app-ability2', $clothing->primaryAbility->name ?? '?')) . "\n" ?>
      </td>
      <td>
        <?= Html::encode(Yii::t('app-ability2', $shoes->primaryAbility->name ?? '?')) . "\n" ?>
      </td>
    </tr>
<?php for ($i = 0; $i < 3; ++$i) { ?>
    <tr>
<?php if ($i === 0) { ?>
      <th scope="row" rowspan="3">
        <?= Html::encode(Yii::t('app', 'Secondary Abilities')) . "\n" ?>
      </th>
<?php } ?>
      <td>
        <?= Html::encode(
          count($headgear->secondaries) >= $i
            ? Yii::t('app-ability2', $headgear->secondaries[$i]->ability->name ?? '(Locked)')
            : ''
        ) . "\n" ?>
      </td>
      <td>
        <?= Html::encode(
          count($clothing->secondaries) >= $i
            ? Yii::t('app-ability2', $clothing->secondaries[$i]->ability->name ?? '(Locked)')
            : ''
        ) . "\n" ?>
      </td>
      <td>
        <?= Html::encode(
          count($shoes->secondaries) >= $i
            ? Yii::t('app-ability2', $shoes->secondaries[$i]->ability->name ?? '(Locked)')
            : ''
        ) . "\n" ?>
      </td>
    </tr>
<?php } ?>
  </tbody>
</table>
