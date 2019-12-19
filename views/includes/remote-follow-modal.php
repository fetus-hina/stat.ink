<?php

declare(strict_types=1);

use app\models\RemoteFollowModalForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="modal fade" id="remoteFollowModal" tabindex="-1" role="dialog" aria-labelledby="remoteFollowModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <?= Html::button(
          '<span aria-hidden="true"><span class="fas fa-fw fa-times"></span></span>',
          [
            'class' => 'close',
            'data' => [
              'dismiss' => 'modal',
            ],
            'aria-label' => Yii::t('app', 'Close'),
          ]
        ) . "\n" ?>
        <h4 class="modal-title" id="remoteFollowModalLabel">
          <?= Html::img(
            '@web/static-assets/ostatus/ostatus.min.svg',
            ['style' => [
              'height' => '1em',
              'width' => 'auto',
              'vertical-align' => 'baseline',
            ]]
          ) . "\n" ?>
          <?= Html::encode(Yii::t('app', 'Remote Follow')) . "\n" ?>
          (@<?= Html::encode($user->screen_name) ?>@<?= Html::encode(Yii::$app->request->hostName) ?>)
        </h4>
      </div>
      <div class="modal-body">
        <p>
          マストドンなどのOStatus対応サービスを利用して、バトル結果を購読することができます。
        </p>
        <p>
          このユーザ（@<?= Html::encode($user->screen_name) ?>@<?= Html::encode(Yii::$app->request->hostName) ?>）をフォローする、
          あなたのアカウント名を「ユーザ名@サーバ」の形式で入力してください。<br>
          例えば、mstdn.jp の利用者であれば「<code>your_id@mstdn.jp</code>」、Pawoo の利用者であれば「<code>your_id@pawoo.net</code>」です。
        </p>
        <hr>
        <div style="margin-top:15px">
<?php $form = RemoteFollowModalForm::factory() ?>
          <?php $_ = ActiveForm::begin(['action' => ['/ostatus/start-remote-follow', 'screen_name' => $user->screen_name]]); echo "\n" ?>
            <?= $_->field($form, 'screen_name')
              ->hiddenInput(['value' => $user->screen_name])
              ->label(false) . "\n" ?>
            <?= $_->field($form, 'account')
              ->textInput(['placeholder' => '例: your_id@mstdn.jp'])
              ->label('あなたのアカウント') . "\n" ?>
            <div class="form-group">
              <input type="submit" value="指定アカウントでこのユーザをフォローする" class="btn btn-primary btn-block">
            </div>
          <?php ActiveForm::end(); echo "\n" ?>
        </div>
      </div>
    </div>
  </div>
</div>
