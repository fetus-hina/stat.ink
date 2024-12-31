<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\BrowserIconWidget;
use app\components\widgets\Icon;
use app\components\widgets\LocationColumnWidget;
use app\components\widgets\MaxmindMessage;
use app\components\widgets\OsIconWidget;
use app\models\UserLoginHistory;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var DataProviderInterface $dataProvider
 * @var View $this
 */

TableResponsiveForceAsset::register($this);

$title = Yii::t('app', 'Login History');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);
?>
<div class="container">
  <h1><?= Html::encode(Yii::t('app', 'Login History')) ?></h1>
  <p>
    <?= Html::a(
      implode(' ', [
        Icon::back(),
        Yii::t('app', 'Back'),
      ]),
      ['user/profile'],
      ['class' => 'btn btn-default']
    ) . "\n" ?>
  </p>
  <p>
    <small><?= Html::encode(Yii::t('app', 'The estimated location may be inaccurate.')) ?></small>
  </p>
  <p>
    <small><?= Html::encode(Yii::t('app', 'Login history will be deleted in {term}.', [
      'term' => Yii::$app->formatter->format('P30D', 'duration'),
    ])) ?></small>
  </p>
  <div class="table-responsive table-responsive-force nobr">
    <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'columns' => [
        [
          'attribute' => 'created_at',
          'format' => 'htmlRelative',
          'label' => Yii::t('app', 'Date Time'),
        ],
        [
          'attribute' => 'method.name',
          'format' => ['translated', 'app'],
          'label' => Yii::t('app', 'Login Method'),
        ],
        [
          'label' => Yii::t('app', 'Location'),
          'format' => 'raw',
          'value' => function (UserLoginHistory $model): string {
            return LocationColumnWidget::widget([
              'remoteAddr' => $model->remote_addr,
              'remoteAddrMasked' => $model->remote_addr_masked,
              'remoteHost' => $model->remote_host,
            ]);
          },
        ],
        [
          'label' => '',
          'format' => 'raw',
          'contentOptions' => ['class' => 'text-center'],
          'value' => function (UserLoginHistory $model): string {
            return BrowserIconWidget::widget([
              'ua' => $model->userAgent->user_agent ?? null,
              'size' => '2em',
            ]);
          },
        ],
        [
          'label' => '',
          'format' => 'raw',
          'contentOptions' => ['class' => 'text-center'],
          'value' => function (UserLoginHistory $model): string {
            return OsIconWidget::widget([
              'ua' => $model->userAgent->user_agent ?? null,
              'size' => '2em',
            ]);
          },
        ],
        [
          'attribute' => 'userAgent.user_agent',
          'label' => Yii::t('app', 'User Agent'),
        ],
      ],
    ]) . "\n" ?>
  </div>
  <?= MaxmindMessage::widget([
    'options' => [
      'tag' => 'p',
      'class' => 'small text-muted',
    ],
  ]) . "\n" ?>
</div>
