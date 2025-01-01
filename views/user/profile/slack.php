<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\EmojifyResourceAsset;
use app\assets\SlackAsset;
use app\components\widgets\Icon;
use app\models\Slack;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

SlackAsset::register($this);

echo Html::tag(
  'h2',
  implode(' ', [
    Icon::slack(),
    Icon::discord(),
    Html::encode(Yii::t('app', 'Slack Integration')),
    Html::a(
      Icon::addSomething(),
      ['slack-add'],
      ['class' => 'btn btn-primary'],
    ),
  ]),
) . "\n";

echo GridView::widget([
  'dataProvider' => new ActiveDataProvider([
    'query' => $user->getSlacks()->with('language'),
    'pagination' => false,
    'sort' => false
  ]),
  'columns' => [
    [
      'label' => Yii::t('app', 'Enabled'),
      'format' => 'raw',
      'value' => function ($model) : string {
        return Html::checkbox(
          sprintf('slack-%d', $model->id),
          !$model->suspended,
          [
            "class" => [ "slack-toggle-enable" ],
            "data" => [
              "toggle" => "toggle",
              "on" => Yii::t('app', 'Enabled'),
              "off" => Yii::t('app', 'Disabled'),
              "id" => $model->id
            ],
            "disabled" => true
          ]
        );
      },
    ],
    [
      'label' => '',
      'format' => 'raw',
      'value' => fn (Slack $model): string => match (true) {
        str_contains($model->webhook_url, '//hooks.slack.com/') => Icon::slack(),
        str_contains($model->webhook_url, '//discord') => Icon::discord(),
        default => Icon::unknown(),
      },
    ],
    [
      'label' => Yii::t('app', 'User Name'),
      'value' => function (Slack $model): string {
        $value = trim((string)$model->username);

        if ($value === '') {
          return Yii::t('app', '(default)');
        }

        return $value;
      },
    ],
    [
      'label' => Yii::t('app', 'Icon'),
      'format' => 'raw',
      'value' => function (Slack $model): string {
        $value = trim((string)$model->icon);

        if ($value === '') {
          return Html::encode(Yii::t('app', '(default)'));
        }

        if (strtolower(substr($value, 0, 4)) === 'http' || substr($value, 0, 2) === '//') {
          return Html::img($value, ['class' => 'emoji emoji-url']);
        }

        if (preg_match('/^:[a-zA-Z0-9+._-]+:$/', $value)) {
          $asset = EmojifyResourceAsset::register($this);
          $fileName = trim((string)$value, ':') . '.png';
          return implode(' ', [
            Html::img(
              Yii::$app->assetManager->getAssetUrl($asset, $fileName),
              ['style' => 'height:2em;width:auto']
            ),
            Html::encode($value),
          ]);
        }

        return Html::encode($value);
      },
    ],
    [
      'label' => Yii::t('app', 'Channel'),
      'value' => function (Slack $model): string {
        $value = trim((string)$model->channel);
        if ($value === '') {
          return Yii::t('app', '(default)');
        }

        return $value;
      },
    ],
    [
      'label' => Yii::t('app', 'Language'),
      'attribute' => 'language.name',
    ],
    [
      'label' => '',
      'format' => 'raw',
      'value' => fn (Slack $model): string => implode(' ', [
        Html::tag(
          'button',
          Html::encode(Yii::t('app', 'Test')),
          [
            'class' => 'slack-test btn btn-info btn-sm',
            'data' => [
              'id' => $model->id,
            ],
            'disabled' => true,
          ]
        ),
        Html::tag(
          'button',
          Html::encode(Yii::t('app', 'Delete')),
          [
            'class' => 'slack-del btn btn-danger btn-sm',
            'data' => [
              'id' => $model->id,
            ],
            'disabled ' => true,
          ]
        ),
      ]),
    ],
  ],
]);
