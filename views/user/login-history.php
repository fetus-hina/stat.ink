<?php
declare(strict_types=1);

use app\components\widgets\FA;
use app\models\UserLoginHistory;
use statink\yii2\jdenticon\Jdenticon;
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
          'label' => Yii::t('app', 'IP address'),
          'format' => 'raw',
          'value' => function (UserLoginHistory $model): string {
            return Html::tag(
              'div',
              implode(' ', [
                $model->remote_addr_masked
                  ? Jdenticon::widget([
                    'hash' => hash('sha256', $model->remote_addr_masked),
                    'params' => [
                      'style' => [
                        'height' => '2em',
                      ],
                    ],
                  ])
                  : '',
                $model->remote_host
                  ? Html::tag(
                    'span',
                    Html::encode(strtolower($model->remote_host)),
                    ['title' => $model->remote_addr, 'class' => 'auto-tooltip']
                  )
                  : (
                    ($model->remote_addr_masked && strpos($model->remote_addr, ':') !== false)
                      // IPv6 : display masked
                      ? Html::tag(
                        'span',
                        Html::encode($model->remote_addr_masked),
                        ['title' => $model->remote_addr, 'class' => 'auto-tooltip']
                      )
                      : Html::encode($model->remote_addr)
                  )
              ]),
              [
                'class' => 'd-flex align-items-center',
              ]
            );
          },
        ],
        [
          'attribute' => 'userAgent.user_agent',
          'label' => Yii::t('app', 'User Agent'),
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
