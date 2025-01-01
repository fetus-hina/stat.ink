<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
    Html::encode(Yii::t('app', 'To use Discord integration, make a webhook on your Discord server first.')),
    Html::encode(Yii::t('app', '(For advanced users)')),
  ]),
) . "\n" ?>
<?= Html::tag(
  'p',
  implode(' ', [
    Yii::t('app', 'Add <code>/slack</code> to the end of the created webhook URL.'),
  ]),
) . "\n" ?>
<?= Html::tag(
  'p',
  implode(' ', [
    Html::tag(
      'strong',
      implode(' ', [
        Html::encode(
          Yii::t('app', 'Set the name, icon, and channel in the settings within Discord.'),
        ),
        Html::encode(
          Yii::t('app', 'Even you set them up in the input fields below they will not work.'),
        ),
      ]),
      ['class' => 'text-danger'],
    ),
    Html::encode(
      Yii::t('app', 'This is a Discord-specific behavior.'),
    ),
  ]),
) . "\n" ?>
