<?php

declare(strict_types=1);

use app\components\widgets\FA;
use app\components\widgets\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var User $user
 * @var View $this
 */

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
      <h2><?= Html::encode(Yii::t('app', 'Login History')) ?></h2>
      <p>
        <?= Html::a(
          implode(' ', [
            Icon::loginHistory(),
            Html::encode(Yii::t('app', 'Login History')),
          ]),
          ['user/login-history'],
          ['class' => 'btn btn-default btn-block text-left']
        ) . "\n" ?>
      </p>
      <h2><?= Html::encode(Yii::t('app', 'Export')) ?> (Splatoon 2)</h2>
      <p><?= implode('', [
        Html::a(
          implode(' ', [
            Icon::fileCsv(),
            Html::encode(Yii::t('app', 'CSV')),
          ]),
          ['download2', 'type' => 'csv'],
          ['class' => 'btn btn-default btn-block text-left']
        ),
        Html::a(
          implode(' ', [
            Icon::fileCsv(),
            Html::encode(Yii::t('app', 'CSV (IkaLog compat.)')),
          ]),
          ['download2', 'type' => 'ikalog-csv'],
          ['class' => 'btn btn-default btn-block text-left']
        ),
        Html::tag(
          'div',
          implode('', [
            Html::a(
              implode(' ', [
                Icon::fileCsv(),
                FA::fas('fish')->fw(),
                Html::encode(Yii::t('app', 'Salmon Run CSV')),
                ' ',
                Html::tag('small', Html::encode('(β)')),
              ]),
              ['download-salmon', 'type' => 'csv'],
              ['class' => 'btn btn-default text-left-important flex-grow-1']
            ),
            Html::a(
              FA::fas('info')->fw(),
              'https://github.com/fetus-hina/stat.ink/blob/master/doc/api-2/export-salmon-csv.md',
              [
                'class' => 'btn btn-default auto-tooltip',
                'title' => Yii::t('app', 'Schema information'),
                'rel' => 'external',
              ]
            ),
          ]),
          ['class' => 'btn-group d-flex']
        ),
      ]) ?></p>
      <h2><?= Html::encode(Yii::t('app', 'Export')) ?> (Splatoon 1)</h2>
      <p><?= implode('', [
        Html::a(
          implode(' ', [
            Icon::fileCsv(),
            Html::encode(Yii::t('app', 'CSV (IkaLog compat.)')),
          ]),
          ['download', 'type' => 'ikalog-csv'],
          ['class' => 'btn btn-default btn-block text-left']
        ),
        Html::a(
          implode(' ', [
            Icon::fileJson(),
            Html::encode(Yii::t('app', 'JSON (IkaLog compat.)')),
          ]),
          ['download', 'type' => 'ikalog-json'],
          ['class' => 'btn btn-default btn-block text-left']
        ),
        $user->isUserJsonReady
          ? Html::a(
            implode(' ', [
              Icon::fileJson(),
              Html::encode(Yii::t('app', 'JSON (stat.ink format, gzipped)')),
            ]),
            ['download', 'type' => 'user-json'],
            ['class' => 'btn btn-default btn-block text-left']
          )
          : Html::tag(
            'button',
            implode(' ', [
              Icon::fileJson(),
              Html::encode(Yii::t('app', 'JSON (stat.ink format, gzipped)')),
            ]),
            ['class' => 'btn btn-default btn-block text-left', 'disabled ' => true]
          ),
      ]) ?></p>
    </div>
  </div>
</div>
