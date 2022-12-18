<?php

use app\models\Battle2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var Battle2 $battle
 * @var View $this
 */

$title = Yii::t('app', 'Edit Your Battle: #{0}', [$battle->id]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerCss(implode('', [
  'th{width:15em}',
  '@media(max-width:30em){th{width:auto}}',
  '.image-container{margin-bottom:15px}',
]));
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
<?php if ($battle->battleImageJudge || $battle->battleImageResult): ?>
  <div class="row">
<?php if ($battle->battleImageJudge): ?>
    <div class="col-xs-12 col-md-6 image-container">
      <?= Html::img(
        $battle->battleImageJudge->url,
        ['style' => ['max-width' => '100%', 'height' => 'auto']]
      ) . "\n" ?>
    </div>
<?php endif; ?>
<?php if ($battle->battleImageResult): ?>
    <div class="col-xs-12 col-md-6 image-container">
      <?= Html::img(
        $battle->battleImageResult->url,
        ['style' => ['max-width' => '100%', 'height' => 'auto']]
      ) . "\n" ?>
    </div>
<?php endif; ?>
  </div>
<?php endif; ?>
  <?php $_ = ActiveForm::begin([
    'action' => ['show-v2/edit-battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
    'id' => 'edit-form',
  ]); echo "\n"; ?>
    <?= $_->field($form, 'xMode')
      ->dropDownList($form->createModeList()) . "\n"
    ?>
    <?= $_->field($form, 'map_id')
      ->dropDownList($maps) . "\n"
    ?>
    <?= $_->field($form, 'weapon_id')
      ->dropDownList($weapons) . "\n"
    ?>
    <?= $_->field($form, 'result')
      ->dropDownList([
        '' => '',
        'win' => Yii::t('app', 'Won'),
        'lose' => Yii::t('app', 'Lost'),
      ]) . "\n"
    ?>
    <?= implode(' / ', [
        Html::tag(
          'label',
          Html::encode(Yii::t('app', 'Kill or Assist')),
          ['class' => 'control-label', 'for' => 'battle2form-kill_or_assist']
        ),
        Html::tag(
          'label',
          Html::encode(Yii::t('app', 'Specials')),
          ['class' => 'control-label', 'for' => 'battle2form-special']
        ),
      ]) . "\n"
    ?>
    <div class="form-inline">
      <?= $_->field($form, 'kill_or_assist')
        ->label(false)
        ->textInput(['style' => 'width:4em']) ?> K /
      <?= $_->field($form, 'special')
        ->label(false)
        ->textInput(['style' => 'width:4em']) ?> SP
    </div>
    <?= implode(' / ', [
        Html::tag(
          'label',
          Html::encode(Yii::t('app', 'Kills')),
          ['class' => 'control-label', 'for' => 'battle2form-kill']
        ),
        Html::tag(
          'label',
          Html::encode(Yii::t('app', 'Deaths')),
          ['class' => 'control-label', 'for' => 'battle2form-death']
        ),
      ]) . "\n"
    ?>
    <div class="form-inline">
      <?= $_->field($form, 'kill')
        ->label(false)
        ->textInput(['style' => 'width:4em']) ?> K /
      <?= $_->field($form, 'death')
        ->label(false)
        ->textInput(['style' => 'width:4em']) ?> D
    </div>
    <?= $_->field($form, 'my_point')
      ->hint(Html::encode(
        sprintf('(%s)', Yii::t('app-rule2', 'Turf War'))
      )) . "\n"
    ?>
    <label><?=
      Html::encode(Yii::t('app', 'Rank'))
    ?></label>
    <div class="form-inline">
      <?= $_->field($form, 'rank_id')
        ->label(false)
        ->dropDownList($ranks) ?> →
      <?= $_->field($form, 'rank_after_id')
        ->label(false)
        ->dropDownList($ranks) . "\n" ?>
    </div>
    <?= $_->field($form, 'link_url')
      ->textInput([
        'type' => 'url',
        'placeholder' => 'https://example.com/',
      ])
      ->hint(
        Html::encode(
          Yii::t('app', 'e.g. YouTube video, like "{0}"', [
            'https://www.youtube.com/watch?v=TjLbFFPF904'
          ])
        )
      ) . "\n"
    ?>
    <?= $_->field($form, 'note')
      ->textArea(['rows' => 7]) . "\n"
    ?>
    <?= $_->field($form, 'private_note')
      ->textArea(['rows' => 7]) . "\n"
    ?>
    <?= Html::submitButton(
        Yii::t('app', 'Update'),
        ['class' => 'btn btn-primary btn-block']
    ) . "\n" ?>
  <?php ActiveForm::end(); echo "\n"; ?>
  <div style="margin-top:15px">
    <?= Html::a(
      Yii::t('app', 'Back'),
      ['show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
      ['class' => 'btn btn-default btn-block']
    ) . "\n" ?>
  </div>
  <hr>
  <div style="margin-top:7.5em;border:1px solid #ccc;border-radius:5px;padding:15px">
    <h2 style="color:#c9302c">
      <?= Html::encode(Yii::t('app', 'Danger Zone')) . "\n" ?>
    </h2>
    <p>
      <?= Html::encode(Yii::t('app', 'You can delete this battle.')) . "\n" ?>
    </p>
    <ul>
      <li>
        <?= Html::encode(Yii::t('app', 'If you delete this battle, it will be gone forever.')) . "\n" ?>
      </li>
      <li>
        <strong style="color:#c9302c">
          <?= Html::encode(Yii::t('app', 'Please do not use this feature to destroy evidence.')) . "\n" ?>
        </strong>
        <?= Html::encode(Yii::t('app', 'This option is provided for deleting an incorrectly-reported battle.')) . "\n" ?>
      </li>
      <li>
        <?= Html::encode(Yii::t('app', 'If you misuse this feature, you will be banned.')) . "\n" ?>
      </li>
    </ul>
    <?php $_ = ActiveForm::begin([
      'action' => ['show-v2/edit-battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
      'id' => 'edit-form',
    ]); echo "\n"; ?>
      <?= Html::hiddenInput('_action', 'delete') . "\n" ?>
      <?= $_->field($delete, 'agree')
        ->label(Yii::t('app', 'I agree. Delete this battle.'))
        ->checkbox(['uncheck' => null]) . "\n"
      ?>
      <?= Html::submitButton(
        Html::encode(Yii::t('app', 'Delete')),
        ['class' => 'btn btn-lg btn-danger btn-block']
      ) . "\n" ?>
    <?php ActiveForm::end(); echo "\n"; ?>
  </div>
</div>
