<?php
use app\assets\TwitterWebIntentsAsset;
use app\components\widgets\JdenticonWidget;
use yii\helpers\Html;
use yii\widgets\DetailView;

$title = Yii::t('app', 'Profile and Settings');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerCss('.btn-block.text-left{text-align:left}');
?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-9">
      <h1>
        <?= Html::encode($title) . "\n" ?>
        <?= Html::a(
          implode('', [
            Html::tag('span', '', ['class' => 'fas fa-fw fa-edit']),
            Html::encode(Yii::t('app', 'Update')),
          ]),
          ['edit-profile'],
          ['class' => 'btn btn-primary']
        ) . "\n" ?>
      </h1>
      <?= $this->render('_profile_profile', ['user' => $user]) . "\n" ?>
      <?= $this->render('_profile_login_with', ['user' => $user]) . "\n" ?>
      <?= $this->render('_profile_slack', ['user' => $user]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-3">
      <h2><?= Html::encode(Yii::t('app', 'Export')) ?> (Splatoon 2)</h2>
      <p><?= implode('', [
        Html::a(
          implode('', [
            Html::tag('span', '', ['class' => 'far fa-fw fa-file-excel']),
            Html::encode(Yii::t('app', 'CSV')),
          ]),
          ['download2', 'type' => 'csv'],
          ['class' => 'btn btn-default btn-block text-left']
        ),
        Html::a(
          implode('', [
            Html::tag('span', '', ['class' => 'far fa-fw fa-file-excel']),
            Html::encode(Yii::t('app', 'CSV (IkaLog compat.)')),
          ]),
          ['download2', 'type' => 'ikalog-csv'],
          ['class' => 'btn btn-default btn-block text-left']
        ),
      ]) ?></p>
      <h2><?= Html::encode(Yii::t('app', 'Export')) ?> (Splatoon 1)</h2>
      <p><?= implode('', [
        Html::a(
          implode('', [
            Html::tag('span', '', ['class' => 'far fa-fw fa-file-excel']),
            Html::encode(Yii::t('app', 'CSV (IkaLog compat.)')),
          ]),
          ['download', 'type' => 'ikalog-csv'],
          ['class' => 'btn btn-default btn-block text-left']
        ),
        Html::a(
          implode('', [
            Html::tag('span', '', ['class' => 'far fa-fw fa-file-code']),
            Html::encode(Yii::t('app', 'JSON (IkaLog compat.)')),
          ]),
          ['download', 'type' => 'ikalog-json'],
          ['class' => 'btn btn-default btn-block text-left']
        ),
        $user->isUserJsonReady
          ? Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'far fa-fw fa-file-code']),
              Html::encode(Yii::t('app', 'JSON (stat.ink format, gzipped)')),
            ]),
            ['download', 'type' => 'user-json'],
            ['class' => 'btn btn-default btn-block text-left']
          )
          : Html::tag(
            'button',
            implode('', [
              Html::tag('span', '', ['class' => 'far fa-fw fa-file-code']),
              Html::encode(Yii::t('app', 'JSON (stat.ink format, gzipped)')),
            ]),
            ['class' => 'btn btn-default btn-block text-left', 'disabled ' => true]
          ),
      ]) ?></p>
    </div>
  </div>
</div>
