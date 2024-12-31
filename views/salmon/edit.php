<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Salmon2 $model
 * @var View $this
 */

$title = Yii::t('app-salmon2', 'Edit job #{jobNumber}', [
    'jobNumber' => Yii::$app->formatter->asInteger($model->id),
]);

$this->title = sprintf('%s | %s', Yii::$app->name, $title);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <p>
    <?= Html::a(
      implode(' ', [
        '<span class="fas fa-fw fa-angle-left"></span>',
        Html::encode(Yii::t('app', 'Back')),
      ]),
      ['salmon/view',
        'screen_name' => $model->user->screen_name,
        'id' => $model->id,
      ],
      ['class' => 'btn btn-default']
    ) . "\n" ?>
  </p>
  <div class="panel panel-default">
    <div class="panel-heading">
      <?= Html::encode(Yii::t('app', 'Edit')) . "\n" ?>
    </div>
    <div class="panel-body">
      Sorry, you can't edit your results at this time.
      (It hasn't implemented yet)
    </div>
  </div>
  <div class="panel panel-danger">
    <div class="panel-heading">
      <?= Html::encode(Yii::t('app', 'Danger Zone')) . "\n" ?>
    </div>
    <div class="panel-body">
      <p>
        <?= Html::encode(Yii::t('app-salmon2', 'You can delete this job.')) . "\n" ?>
      </p>
      <ul>
        <li>
          <?= Html::encode(Yii::t('app-salmon2', 'If you delete this job, it will be gone forever.')) . "\n" ?>
        </li>
      </ul>
      <?php $_ = ActiveForm::begin([
        'action' => ['salmon/delete',
          'screen_name' => $model->user->screen_name,
          'id' => $model->id,
        ],
        'id' => 'delete-form',
      ]); echo "\n"; ?>
        <?= $_->field($deleteForm, 'agree')
          ->label(Yii::t('app-salmon2', 'I agree. Delete this job.'))
          ->checkbox(['uncheck' => null]) . "\n"
        ?>
        <?= Html::submitButton(
          Html::encode(Yii::t('app', 'Delete')),
          ['class' => 'btn btn-danger btn-block']
        ) . "\n" ?>
      <?php ActiveForm::end(); echo "\n"; ?>
    </div>
  </div>
</div>
