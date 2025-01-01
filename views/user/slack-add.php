<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\SlackAddForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var SlackAddForm $form
 * @var View $this
 * @var array<int, string> $languages
 */

$title = Yii::t('app', 'Add Slack Integration');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerCss(
  vsprintf('#notice .tab-content>.panel-default{%s}', [
    Html::cssStyleFromArray([
      'border-top' => '0',
      'border-top-left-radius' => '0',
      'border-top-right-radius' => '0',
    ]),
  ]),
);

?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <div class="mb-3" id="notice">
    <?= Tabs::widget([
      'encodeLabels' => false,
      'itemOptions' => [
        'class' => [
          'p-3',
          'pb-0',
          'panel',
          'panel-default',
        ],
      ],
      'items' => [
        [
          'label' => implode(' ', [
            Icon::slack(),
            Html::encode(Yii::t('app', 'Slack')),
          ]),
          'content' => $this->render('slack-add/tabs/slack'),
        ],
        [
          'label' => implode(' ', [
            Icon::discord(),
            Html::encode(Yii::t('app', 'Discord')),
          ]),
          'content' => $this->render('slack-add/tabs/discord'),
        ],
      ],
    ]) . "\n" ?>
  </div>

  <?php $_ = ActiveForm::begin(['id' => 'add-form', 'action' => ['slack-add']]); echo "\n" ?>
    <?= $_->field($form, 'webhook_url')
      ->input('text', [
        'placeholder' => 'https://hooks.slack.com/services/AAAAAAAAA/BBBBBBBBB/CCCCCCCCCCCCCCCCCCCCCCCC',
      ])
      ->hint(Yii::t('app', 'You can specify Discord\'s Slack compatible endpoint URL as well.')) . "\n"
    ?>
    <?= $_->field($form, 'username')
      ->hint(Yii::t('app', 'If omitted, the name set in the webhook configuration will be used.')) . "\n"
    ?>
    <?= $_->field($form, 'icon')
      ->input('text', [
        'placeholder' => ':emoji:'
      ])
      ->hint(Yii::t('app', '<a href="http://www.emoji-cheat-sheet.com/" target="_blank">Cheat sheet</a>. If omitted, the default icon will be used.')) . "\n"
    ?>
    <?= $_->field($form, 'channel')
      ->input('text', [
        'placeholder' => '#splatoon'
      ])
      ->hint(Yii::t('app', 'If omitted, the channel set in the webhook configuration will be used.')) . "\n"
    ?>
    <?= $_->field($form, 'language_id')
      ->dropDownList($languages)
      ->hint(Yii::t('app', 'The post will be in the language set here.')) . "\n"
    ?>
    <?= Html::submitButton(
      Html::encode(Yii::t('app', 'Add')),
      ['class' => 'btn btn-lg btn-primary btn-block']
    ) . "\n" ?>
  <?php ActiveForm::end(); echo "\n" ?>

  <div style="margin-top:15px">
    <?= Html::a(
      Html::encode(Yii::t('app', 'Back')),
      ['user/profile'],
      ['class' => 'btn btn-lg btn-default btn-block']
    ) . "\n" ?>
  </div>
</div>
