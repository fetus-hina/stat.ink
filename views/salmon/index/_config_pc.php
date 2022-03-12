<?php

declare(strict_types=1);

use app\assets\SalmonWorkListConfigAsset;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

SalmonWorkListConfigAsset::register($this);

?>
<div class="row">
  <div class="col-xs-12" id="table-config">
    <div>
      <label>
        <input type="checkbox" id="table-hscroll" value="1">
        <?= Html::encode(Yii::t('app', 'Always enable horizontal scroll')) . "\n" ?>
      <label>
    </div>
    <div class="row"><?php
      $_list = [
        'cell-splatnet' => Yii::t('app', 'SplatNet #'),
        'cell-map' => Yii::t('app', 'Stage'),
        'cell-map-short' => Yii::t('app', 'Stage (Short)'),
        'cell-special' => Yii::t('app', 'Special'),
        'cell-result' => Yii::t('app', 'Result'),
        'cell-golden' => Yii::t('app-salmon2', 'Golden Eggs'),
        'cell-golden-wave' => Yii::t('app-salmon2', 'Golden Eggs per Wave'),
        'cell-golden-total' => Yii::t('app-salmon2', 'Team total Golden Eggs'),
        'cell-golden-total-wave' => Yii::t('app-salmon2', 'Team total Golden Eggs per Wave'),
        'cell-power' => Yii::t('app-salmon2', 'Power Eggs'),
        'cell-power-wave' => Yii::t('app-salmon2', 'Power Eggs per Wave'),
        'cell-power-total' => Yii::t('app-salmon2', 'Team total Power Eggs'),
        'cell-power-total-wave' => Yii::t('app-salmon2', 'Team total Power Eggs per Wave'),
        'cell-rescue' => Yii::t('app-salmon2', 'Rescues'),
        'cell-death' => Yii::t('app-salmon2', 'Deaths'),
        'cell-danger-rate' => Yii::t('app-salmon2', 'Hazard Level'),
        'cell-title' => Yii::t('app', 'Title'),
        'cell-title-after' => Yii::t('app', 'Title (After)'),
        'cell-datetime' => Yii::t('app', 'Date Time'),
        'cell-reltime' => Yii::t('app', 'Relative Time'),
      ];
      foreach ($_list as $k => $v) {
        echo Html::tag(
          'div',
          Html::tag(
            'label',
            sprintf(
              '%s %s',
              Html::tag('input', '', ['type' => 'checkbox', 'class' => 'table-config-chk', 'data-klass' => $k]),
              Html::encode($v)
            )
          ),
          ['class' => 'col-xs-6 col-sm-4 col-lg-3']
        );
      }
    ?></div>
  </div>
</div>
