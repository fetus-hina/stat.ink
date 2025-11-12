<?php

/**
 * @copyright Copyright (C) 2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

use function implode;
use function vsprintf;

/**
 * @var View $this
 * @var string $searchValue
 */

$this->context->layout = 'main';

$title = Yii::t('app', 'Search Unregistered Players');

$this->title = vsprintf('%s | %s', [
    Yii::$app->name,
    $title,
]);

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <h1>
        <?= Icon::search() ?>
        <?= Html::encode($title) ?>
      </h1>
      <p class="lead">
        <?= Html::encode(Yii::t('app', 'Search for unregistered player statistics by their Splashtag')) ?>
      </p>
    </div>
  </div>
  
  <div class="row">
    <div class="col-xs-12 col-md-8 col-lg-6">
      
      <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
          <?= Html::encode(Yii::$app->session->getFlash('error')) ?>
        </div>
      <?php endif; ?>

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <?= Icon::splatoon3() ?>
            <?= Html::encode(Yii::t('app', 'Player Search')) ?>
          </h3>
        </div>
        <div class="panel-body">
          
          <?php $form = ActiveForm::begin([
            'method' => 'get',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
              'template' => '{label}<div class="col-sm-9">{input}{error}</div>',
              'labelOptions' => ['class' => 'col-sm-3 control-label'],
            ],
          ]); ?>
          
          <div class="form-group">
            <label class="col-sm-3 control-label" for="splashtag-input">
              <?= Html::encode(Yii::t('app', 'Splashtag')) ?>
            </label>
            <div class="col-sm-9">
              <?= Html::textInput('splashtag', $searchValue, [
                'class' => 'form-control',
                'id' => 'splashtag-input',
                'placeholder' => 'Username#1234',
                'pattern' => '.+#\d+',
                'title' => Yii::t('app', 'Enter splashtag in format: Username#1234'),
                'required' => true,
              ]) ?>
              <div class="help-block">
                <?= Html::encode(Yii::t('app', 'Enter the exact splashtag including the # symbol (e.g., Player#1234)')) ?>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
              <?= Html::submitButton(
                implode(' ', [
                  Icon::search(),
                  Html::encode(Yii::t('app', 'Search Player')),
                ]),
                ['class' => 'btn btn-primary']
              ) ?>
            </div>
          </div>
          
          <?php ActiveForm::end(); ?>
          
        </div>
      </div>

      <div class="panel panel-info">
        <div class="panel-heading">
          <h4 class="panel-title">
            <?= Icon::info() ?>
            <?= Html::encode(Yii::t('app', 'How It Works')) ?>
          </h4>
        </div>
        <div class="panel-body">
          <ul>
            <li><?= Html::encode(Yii::t('app', 'Enter the exact Splashtag (Username#1234) of the player you want to find')) ?></li>
            <li><?= Html::encode(Yii::t('app', 'The player must have appeared in at least 5 public battles uploaded to stat.ink')) ?></li>
            <li><?= Html::encode(Yii::t('app', 'Only data from non-private battles is included to respect privacy')) ?></li>
            <li><?= Html::encode(Yii::t('app', 'Statistics are aggregated from all registered users who played with this player')) ?></li>
          </ul>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <?= Icon::link() ?>
            <?= Html::encode(Yii::t('app', 'Direct Access')) ?>
          </h4>
        </div>
        <div class="panel-body">
          <p class="small">
            <?= Html::encode(Yii::t('app', 'You can also access player stats directly using these URL formats:')) ?>
          </p>
          <ul class="small">
            <li>
              <code>/unregistered-player-v3/by-splashtag/Username%231234</code><br>
              <span class="text-muted"><?= Html::encode(Yii::t('app', 'Replace Username#1234 with the actual splashtag (URL-encoded)')) ?></span>
            </li>
          </ul>
        </div>
      </div>

    </div>
    
    <div class="col-xs-12 col-md-4 col-lg-6">
      <?= AdWidget::widget() ?>
    </div>
  </div>
</div>