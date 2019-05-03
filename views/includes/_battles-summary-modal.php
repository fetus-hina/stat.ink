<?php
declare(strict_types=1);

use app\assets\AppAsset;
use app\components\widgets\FA;
use yii\helpers\Html;
?>
<?= Html::beginTag('div', [
  'id' => 'battles-summary-modal',
  'class' => ['modal', 'fade'],
  'tabindex' => '-1',
  'role' => 'dialog',
]) . "\n" ?>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <?= Html::tag(
          'button',
          (string)FA::fas('times')->fw(),
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
        <div class="row">
          <div class="col-xs-12 col-md-7">
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
            <table class="table table-striped table-hover" style="margin:15px auto">
              <tbody>
<?php foreach ($data as $key => $label): ?>
                <tr>
                  <td style="width:50%" class="text-right"><?= $label ?> :</td>
                  <td><?= Html::tag('span', '', ['data' => ['key' => $key]]) ?></td>
                </tr>
<?php endforeach ?>
              </tbody>
            </table>
          </div>
          <div class="col-xs-12 col-md-5">
            <p>
              <?= Html::encode(Yii::t('app', 'Legends')) ?>:<br>
              <?= Html::img(
                Yii::$app->assetManager->getAssetUrl(
                  Yii::$app->assetManager->getBundle(AppAsset::class),
                  'summary-legends.png'
                ),
                [
                  'style' => [
                    'width' => '100%',
                    'max-width' => '226px',
                    'height' => 'auto',
                  ],
                ]
              ) . "\n" ?>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
