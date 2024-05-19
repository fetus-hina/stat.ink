<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\ProfileForm;
use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
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
  <?= $this->render('profile/alert-versions') . "\n" ?>

  <?php $_ = ActiveForm::begin(['id' => 'update-form', 'action' => ['edit-profile']]); echo "\n" ?>
    <?= $_->field($form, 'name') . "\n" ?>

    <?= $_->field($form, 'hide_data_on_toppage')
      ->dropDownList([
        '0' => sprintf('%s (%s)', Yii::t('app', 'No'), Yii::t('app', 'Show your data on the top page')),
        '1' => sprintf('%s (%s)', Yii::t('app', 'Yes'), Yii::t('app', 'Hide your data on the top page')),
      ])
      ->hint(
        implode('<br>', [
          Yii::t('app', 'Your data will no longer appear in the public list on the top page.'),
          Yii::t('app', 'Your page will still be public, but it will be harder to access.'),
        ]),
      ) . "\n"
    ?>

    <?= $_->field($form, 'blackout')
      ->label(
        implode(' ', [
          Icon::splatoon1(),
          Icon::splatoon2(),
          Html::encode(Yii::t('app', 'Black out other players from the result image')),
        ]),
      )
      ->dropDownList([
        User::BLACKOUT_NOT_BLACKOUT => Yii::t('app', 'No black out'),
        User::BLACKOUT_NOT_PRIVATE  => Yii::t('app', 'Black out except private battle'),
        User::BLACKOUT_NOT_FRIEND   => Yii::t('app', 'Black out except private battle and teammate on squad battle (tri or quad)'),
        User::BLACKOUT_ALWAYS       => Yii::t('app', 'Black out other players')
      ]) . "\n"
    ?>

    <div class="row">
      <div class="col-xs-12 col-sm-11 col-sm-push-1">
        <?= $this->render('profile/profile/blackout/hint', ['id' => 'blackout-info']) . "\n" ?>
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

    <?= $_->field($form, 'blackout_list')
      ->label(
        implode(' ', [
          Icon::splatoon2(),
          Html::encode(Yii::t('app', 'Black out other players from the details list')),
        ]),
      )
      ->dropDownList([
        User::BLACKOUT_NOT_BLACKOUT => Yii::t('app', 'No black out'),
        User::BLACKOUT_NOT_PRIVATE  => Yii::t('app', 'Black out except private battle'),
        User::BLACKOUT_NOT_FRIEND   => Yii::t('app', 'Black out except private battle and teammate on league battle (4 players)'),
        User::BLACKOUT_ALWAYS       => Yii::t('app', 'Black out other players')
      ]) . "\n"
    ?>

    <div class="row">
      <div class="col-xs-12 col-sm-11 col-sm-push-1">
        <?= $this->render('profile/profile/blackout/hint.php', [
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

    <?= $_->field($form, 'link_mode_id')
      ->label(
        implode(' ', [
          Icon::splatoon2(),
          Html::encode(Yii::t('app', 'Link from other user\'s results')),
        ]),
      )
      ->dropDownList($form->linkModes) . "\n"
    ?>

    <?= $_->field($form, 'region_id')
      ->label(
        implode(' ', [
          Icon::splatoon1(),
          Html::encode(Yii::t('app', 'Region (used for Splatfest)')),
        ]),
      )
      ->dropDownList($regions) . "\n" ?>

    <?= $_->field($form, 'default_language_id')
      ->label(
        implode(' ', [
          Icon::splatoon1(),
          Html::encode(Yii::t('app', 'Language (used for OStatus)')),
        ]),
      )
      ->dropDownList($languages) . "\n"
    ?>

    <?= $_->field($form, 'nnid') . "\n" ?>

    <?= $_->field($form, 'sw_friend_code', [
      'inputTemplate' => '<div class="input-group"><span class="input-group-addon">SW-</span>{input}</div>'
    ]) . "\n" ?>

    <?= $_
      ->field(
        $form, 
        'twitter',
        [
          'inputTemplate' => Html::tag(
            'div',
            implode('', [
              Html::tag(
                'span',
                vsprintf('%s @', [
                  Icon::twitter(),
                ]),
                ['class' => 'input-group-addon'],
              ),
              '{input}',
            ]),
            ['class' => 'input-group'],
          ),
        ],
      )
      ->hint(
        Yii::t('app', 'This information will be public. Integration for "log in with twitter" can be done from the profile page.')
      ) . "\n"
    ?>

    <?= $_->field($form, 'ikanakama2', [
      'inputTemplate' => '<div class="input-group"><span class="input-group-addon">https://ikanakama.ink/users/</span>{input}</div>'
    ]) . "\n" ?>

    <?= $_->field($form, 'env')
      ->label(
        implode(' ', [
          Icon::splatoon1(),
          Html::encode(Yii::t('app', 'Capture Environment')),
        ]),
      )
      ->textArea([
        'style' => [
          'height' => '10em',
        ],
      ])->hint(
        Yii::t('app', 'Please tell us about your capture environment and communication between your Wii U and User Agent (e.g. IkaLog). This information will be public.')
      ) . "\n"
    ?>

    <?= Html::submitButton(
      Yii::t('app', 'Update'),
      ['class' => 'btn btn-lg btn-primary btn-block my-3']
    ) . "\n" ?>
  <?php ActiveForm::end(); echo "\n" ?>

  <div class="my-3">
    <?= Html::a(
      Yii::t('app', 'Back'),
      ['user/profile'],
      ['class' => 'btn btn-lg btn-default btn-block']
    ) . "\n" ?>
  </div>
</div>
