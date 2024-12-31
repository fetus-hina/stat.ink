<?php

/**
 * @copyright Copyright (C) 2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\models\RenameScreenNameForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var RenameScreenNameForm $model
 * @var View $this
 */

$title = Yii::t('app', 'Update Your Screen Name');
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>
  <div class="alert alert-danger mb-3">
    <p class="mb-2">
      <?= Yii::t('app', 'If you change the screen name with this form, the change will take effect immediately.') . "\n" ?>
    </p>
    <ul>
      <li class="mb-2">
        <?= Yii::t('app', 'If you have pages that you are sharing with URLs, most of them will be broken links.') . "\n" ?>
        <?= Yii::t('app', 'No redirects from old URLs will be made.') . "\n" ?>
      </li>
      <li class="mb-2">
        <?= Yii::t('app', 'The old name will be available immediately for reuse.') . "\n" ?>
      </li>
      <li class="mb-2">
        <?= Yii::t('app', 'If you have registered your login information in Password Manager, do not forget to update it.') . "\n" ?>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-6">
      <?php $form = ActiveForm::begin(); echo "\n" ?>
        <?= $form->field($model, 'screen_name')
          ->hint(Yii::t('app', '<code>@id</code> (without <code>@</code>), case sensitive.'))
          ->textInput() . "\n"
        ?>
        <?= Html::submitButton(
          Html::encode(Yii::t('app', 'Update')),
          ['class' => 'btn btn-primary btn-block']
        ) . "\n" ?>
      <?php ActiveForm::end(); echo "\n" ?>

      <div style="margin-top:15px">
        <?= Html::a(
          Yii::t('app', 'Back'),
          ['profile'],
          ['class' => 'btn btn-default btn-block']
        ) . "\n" ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6">
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
