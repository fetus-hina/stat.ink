<?php

declare(strict_types=1);

use app\components\helpers\Html;
use app\models\Battle;
use app\models\BattleDeleteForm;
use app\models\BattleForm;
use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var Battle $battle
 * @var BattleDeleteForm $delete
 * @var BattleForm $form
 * @var User $user
 * @var View $this
 * @var array<string, array<string|int, string>> $rules
 * @var array<string|int, string> $lobbies
 * @var array<string|int, string> $maps
 * @var array<string|int, string> $weapons
 */

$title = Yii::t('app', 'Edit Your Battle: #{0}', [
  $battle->id,
]);

$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

<?php if ($battle->battleImageJudge || $battle->battleImageResult): ?>
  <div class="row">
<?php if ($battle->battleImageJudge): ?>
    <div class="col-xs-12 col-md-6 image-container">
      <?= Html::img($battle->battleImageJudge->url, ['style' => 'max-width:100%;height:auto']) . "\n" ?>
    </div>
<?php endif ?>
<?php if ($battle->battleImageResult): ?>
    <div class="col-xs-12 col-md-6 image-container">
      <?= Html::img($battle->battleImageResult->url, ['style' => 'max-width:100%;height:auto']) . "\n" ?>
    </div>
<?php endif ?>
  </div>
<?php endif ?>

  <?php $_ = ActiveForm::begin(['id' => 'edit-form', 'action' => ['show/edit-battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id]]); echo "\n" ?>
    <table class="table table-striped">
      <tbody>
        <tr>
          <th><?= Html::encode(Yii::t('app', 'Lobby')) ?></th>
          <td><?= $_->field($form, 'lobby_id')->label(false)->dropDownList($lobbies) ?></td>
        </tr>
        <tr>
          <th><?= Html::encode(Yii::t('app', 'Mode')) ?></th>
          <td><?= $_->field($form, 'rule_id')->label(false)->dropDownList($rules) ?></td>
        </tr>
        <tr>
          <th><?= Html::encode(Yii::t('app', 'Stage')) ?></th>
          <td><?= $_->field($form, 'map_id')->label(false)->dropDownList($maps) ?></td>
        </tr>
        <tr>
          <th><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
          <td><?= $_->field($form, 'weapon_id')->label(false)->dropDownList($weapons) ?></td>
        </tr>
        <tr>
          <th><?= Html::encode(Yii::t('app', 'URL related to this battle')) ?></th>
          <td><?= $_->field($form, 'link_url')
            ->label(false)
            ->input('url', ['placeholder' => 'https://example.com/'])
            ->hint(Yii::t('app', 'e.g. YouTube video, like "{0}"', ['https://www.youtube.com/watch?v=TjLbFFPF904']))
          ?></td>
        </tr>
        <tr>
          <th><?= Html::encode(Yii::t('app', 'Note (public)')) ?></th>
          <td><?= $_->field($form, 'note')
            ->label(false)
            ->textArea(['rows' => 7])
          ?></td>
        </tr>
        <tr>
          <th><?= Html::encode(Yii::t('app', 'Note (private)')) ?></th>
          <td><?= $_->field($form, 'private_note')
            ->label(false)
            ->textArea(['rows' => 7])
          ?></td>
        </tr>
      </tbody>
    </table>
    <?= Html::submitButton(
      Html::encode(Yii::t('app', 'Update')),
      ['class' => 'btn btn-lg btn-primary btn-block']
    ) . "\n" ?>
  <?php ActiveForm::end(); echo "\n" ?>
  <div style="margin-top:15px">
    <?= Html::a(
      Html::encode(Yii::t('app', 'Back')),
      ['show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
      ['class' => 'btn btn-lg btn-default btn-block']
    ) . "\n" ?>
  </div>

  <hr>

  <div style="margin-top:7.5em;border:1px solid #ccc;border-radius:5px;padding:15px">
    <h2 style="color:#c9302c"><?= Html::encode(Yii::t('app', 'Danger Zone')) ?></h2>
    <p><?= Html::encode(Yii::t('app', 'You can delete this battle.')) ?></p>
    <ul>
      <li>
        <?= Html::encode(Yii::t('app', 'If you delete this battle, it will be gone forever.')) . "\n" ?>
      </li>
      <li>
        <strong style="color:#c9302c"><?= Html::encode(Yii::t('app', 'Please do not use this feature to destroy evidence.')) ?></strong>
        <?= Html::encode(Yii::t('app', 'This option is provided for deleting an incorrectly-reported battle.')) . "\n" ?>
      </li>
      <li>
        <?= Html::encode(Yii::t('app', 'If you misuse this feature, you will be banned.')) ?>
      </li>
    </ul>
    <?php $_ = ActiveForm::begin(['id' => "delete-form", 'action' => ['show/edit-battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id]]); echo "\n" ?>
      <?= Html::hiddenInput('_action', 'delete') . "\n" ?>
      <?= $_->field($delete, 'agree')
        ->label(Yii::t('app', 'I agree. Delete this battle.'))
        ->checkbox(['value' => 'yes', 'uncheck' => null]) . "\n" ?>
      <?= Html::submitButton(
        Html::encode(Yii::t('app', 'Delete')),
        ['class' => 'btn btn-lg btn-danger btn-block']
      ) . "\n" ?>
    <?php ActiveForm::end() ?>
  </div>
</div>
<?php $this->registerCss(<<<'CSS'
th{width:15em}
@media(max-width:30em){th{width:auto}}
.image-container{margin-bottom:15px}
CSS
) ?>
