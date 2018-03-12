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
  'min' => Html::encode(Yii::t('app', 'Minimum')),
  'q1'  => Yii::t('app', 'Q<sub>1/4</sub>'),
  'q2'  => Html::encode(Yii::t('app', 'Median')),
  'q3'  => Yii::t('app', 'Q<sub>3/4</sub>'),
  'max' => Html::encode(Yii::t('app', 'Maximum')),
  'avg' => Html::encode(Yii::t('app', 'Average')),
  'stddev' => Html::encode(Yii::t('app', 'σ')),
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
