<?php

declare(strict_types=1);

use app\assets\InlineListAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

InlineListAsset::register($this);

?>
<?= Html::tag(
  'p',
  implode(' ', [
    Html::encode(Yii::t('app', 'To use Slack integration, you need to configure Slack\'s "Incoming Webhook" first.')),
    Html::encode(Yii::t('app', '(For advanced users)')),
  ]),
) . "\n" ?>
<?= Html::tag(
  'ul',
  implode('', [
    Html::tag(
      'li',
      Html::a(
        Html::encode(Yii::t('app', 'About Incoming Webhook')),
        'https://api.slack.com/incoming-webhooks',
        [
          'target' => '_blank',
          'rel' => 'noopener',
        ],
      ),
    ),
    Html::tag(
      'li',
      Html::a(
        Html::encode(Yii::t('app', 'Create new webhook')),
        'https://my.slack.com/services/new/incoming-webhook/',
        [
          'target' => '_blank',
          'rel' => 'noopener',
        ],
      ),
    ),
  ]),
  ['class' => 'inline-list mb-3'],
) . "\n" ?>
