<?php
declare(strict_types=1);

use app\components\widgets\FA;
use app\components\widgets\LocationColumnWidget;
use app\models\UserLoginHistory;
use yii\grid\GridView;
use yii\helpers\Html;

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
        FA::fas('angle-double-left')->fw(),
        Yii::t('app', 'Back'),
      ]),
      ['user/profile'],
      ['class' => 'btn btn-default']
    ) . "\n" ?>
  </p>
  <p>
    <small><?= Html::encode(Yii::t('app', 'The estimation accuracy of the location is not so good.')) ?></small>
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
          'attribute' => 'userAgent.user_agent',
          'label' => Yii::t('app', 'User Agent'),
        ],
      ],
    ]) . "\n" ?>
  </div>
  <p>
    <small>
      This product includes GeoLite2 data created by MaxMind, available from
      <a href="https://www.maxmind.com">https://www.maxmind.com</a>.
    </small>
  </p>
</div>
