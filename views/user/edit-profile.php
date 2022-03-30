<?php

declare(strict_types=1);

use app\models\ProfileForm;
use app\models\User;
use yii\bootstrap\ActiveForm;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var ProfileForm $form
 * @var View $this
 * @var array<int, string> $languages
 * @var array<int, string> $regions
 */

$title = Yii::t('app', 'Update Your Profile');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?php $_ = ActiveForm::begin(['id' => 'update-form', 'action' => ['edit-profile']]); echo "\n" ?>
    <?= $_->field($form, 'name') . "\n" ?>

    <?= $_->field($form, 'blackout')->dropDownList([
      User::BLACKOUT_NOT_BLACKOUT => Yii::t('app', 'No black out'),
      User::BLACKOUT_NOT_PRIVATE  => Yii::t('app', 'Black out except private battle'),
      User::BLACKOUT_NOT_FRIEND   => Yii::t('app', 'Black out except private battle and teammate on squad battle (tri or quad)'),
      User::BLACKOUT_ALWAYS       => Yii::t('app', 'Black out other players')
    ]) . "\n" ?>

    <div class="row">
      <div class="col-xs-12 col-sm-11 col-sm-push-1">
        <?= $this->render('_blackout-hint', ['id' => 'blackout-info']) . "\n" ?>
<?php $this->registerJs(<<<'JS'
(function($){
  "use strict";
  $('#profileform-blackout').change(function(){
    updateBlackOutHint($(this).val(),'#blackout-info')
  }).change();
})(jQuery);
JS
); ?>
      </div>
    </div>

    <?= $_->field($form, 'blackout_list')->dropDownList([
      User::BLACKOUT_NOT_BLACKOUT => Yii::t('app', 'No black out'),
      User::BLACKOUT_NOT_PRIVATE  => Yii::t('app', 'Black out except private battle'),
      User::BLACKOUT_NOT_FRIEND   => Yii::t('app', 'Black out except private battle and teammate on league battle (4 players)'),
      User::BLACKOUT_ALWAYS       => Yii::t('app', 'Black out other players')
    ]) . "\n" ?>

    <div class="row">
      <div class="col-xs-12 col-sm-11 col-sm-push-1">
        <?= $this->render('_blackout-hint', [
          'mode' => 'splatoon2',
          'id' => 'blackout-info2'
        ]) . "\n" ?>
<?php $this->registerJs(<<<'JS'
(function($){
  "use strict";
  $('#profileform-blackout_list').change(function(){
    updateBlackOutHint($(this).val(),'#blackout-info2')
  }).change();
})(jQuery);
JS
) ?>
      </div>
    </div>

    <?= $_->field($form, 'link_mode_id')->dropDownList($form->getLinkModes()) . "\n" ?>

    <?= $_->field($form, 'region_id')->dropDownList($regions) . "\n" ?>

    <?= $_->field($form, 'default_language_id')->dropDownList($languages) . "\n" ?>

    <?= $_->field($form, 'nnid') . "\n" ?>

    <?= $_->field($form, 'sw_friend_code', [
      'inputTemplate' => '<div class="input-group"><span class="input-group-addon">SW-</span>{input}</div>'
    ]) . "\n" ?>

    <?= $_->field($form, 'twitter', [
      'inputTemplate' => '<div class="input-group"><span class="input-group-addon"><span class="fab fa-twitter left"></span>@</span>{input}</div>',
    ])->hint(
      Yii::t('app', 'This information will be public. Integration for "log in with twitter" can be done from the profile page.')
    ) . "\n" ?>

    <?= $_->field($form, 'ikanakama2', [
      'inputTemplate' => '<div class="input-group"><span class="input-group-addon">https://ikanakama.ink/users/</span>{input}</div>'
    ]) . "\n" ?>

    <?= $_->field($form, 'env')->textArea([
      'style' => 'height:10em'
    ])->hint(
      Yii::t('app', 'Please tell us about your capture environment and communication between your Wii U and User Agent (e.g. IkaLog). This information will be public.')
    ) . "\n" ?>

    <?= Html::submitButton(
      Yii::t('app', 'Update'),
      ['class' => 'btn btn-lg btn-primary btn-block']
    ) . "\n" ?>
  <?php ActiveForm::end(); echo "\n" ?>

  <div style="margin-top:15px">
    <?= Html::a(
      Yii::t('app', 'Back'),
      ['user/profile'],
      ['class' => 'btn btn-lg btn-default btn-block']
    ) . "\n" ?>
  </div>
</div>
