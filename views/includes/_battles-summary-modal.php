<?php
use yii\helpers\Html;
?>
<?= Html::beginTag('div', [
  'id' => 'battles-summary-modal',
  'class' => ['modal', 'fade'],
  'tabindex' => '-1',
  'role' => 'dialog',
]) . "\n" ?>
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <?= Html::tag(
          'button',
          Html::tag('span', '', ['class' => ['fa', 'fa-fw', 'fa-times'], 'aria-hidden' => 'true']),
          [
            'type' => 'button',
            'class' => 'close',
            'data' => [
              'dismiss' => 'modal',
            ],
            'aria-label' => Yii::t('app', 'Close'),
          ]
        ) . "\n" ?>
        <h4 class="modal-title">title</h4>
      </div>
      <div class="modal-body">
        <div class="box-plot">
        </div>
<?php $data = [
  'max' => Html::encode(Yii::t('app', 'Maximum')),
  'pct95' => Html::encode(Yii::t('app', '{percentile} Percentile', ['percentile' => '95'])),
  'q3'  => Yii::t('app', 'Q<sub>3/4</sub>'),
  'q2'  => Html::encode(Yii::t('app', 'Median')),
  'q1'  => Yii::t('app', 'Q<sub>1/4</sub>'),
  'pct5' => Html::encode(Yii::t('app', '{percentile} Percentile', ['percentile' => '5'])),
  'min' => Html::encode(Yii::t('app', 'Minimum')),
  'iqr' => Html::encode(Yii::t('app', 'IQR')),
  'avg' => Html::encode(Yii::t('app', 'Average')),
  'stddev' => Html::encode(Yii::t('app', 'Std Dev')),
]; ?>
        <table class="table table-striped table-hover" style="width:auto!important;margin:15px auto">
          <tbody>
<?php foreach ($data as $key => $label): ?>
            <tr>
              <td class="text-right"><?= $label ?> :</td>
              <td><?= Html::tag('span', '', ['data' => ['key' => $key]]) ?></td>
            </tr>
<?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
