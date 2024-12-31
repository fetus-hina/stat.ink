<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\AppAsset;
use app\assets\BootstrapNotifyAsset;
use app\components\helpers\I18n;
use app\components\widgets\ColorSchemeDialog;
use app\components\widgets\CookieAlert;
use app\components\widgets\LanguageDialog;
use app\components\widgets\TimezoneDialog;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var string $content
 */

AppAsset::register($this);
Yii::$app->theme->registerAssets($this);

$flashes = Yii::$app->getSession()->getAllFlashes();
if ($flashes) {
  BootstrapNotifyAsset::register($this);
  foreach ($flashes as $key => $messages) {
    if (is_array($messages)) {
      $i = 0;
      foreach ($messages as $message) {
        $this->registerJs(
          sprintf(
            '(function($){$.notify(%s)})(jQuery);',
            Json::encode([
              'message' => Html::encode($message),
              'type' => Html::encode($key),
            ])
          ),
        );
      }
    } else {
      $this->registerJs(
        sprintf(
          '(function($){$.notify(%s,%s)})(jQuery);',
          Json::encode([
            'message' => Html::encode($messages),
          ]),
          Json::encode([
            'type' => Html::encode($key),
            'z_index' => 11031,
          ])
        )
      );
    }
  }
}

$request = Yii::$app->request;
$isPjax = $request->isPjax;

$ua = trim((string)$request->userAgent);
$isApple = str_contains($ua, 'iPad') || str_contains($ua, 'iPhone') || str_contains($ua, 'Mac OS X');

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<?= Html::beginTag('html', [
  'lang' => preg_replace('/@.+$/', '', Yii::$app->language),
  'data' => [
    'timezone' => (string)Yii::$app->timeZone,
    'calendar' => (string)Yii::$app->localeCalendar,
  ],
]) . "\n" ?>
  <?= Html::beginTag('head', [
    'prefix' => 'og: https://ogp.me/ns#',
  ]) . "\n" ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no,email=no,address=no">
    <?= Html::csrfMetaTags() ?>
    <?= Html::tag(
      'title',
      Html::encode(trim((string)$this->title) === '' ? Yii::$app->name : $this->title)
    ) . "\n" ?>
    <?= I18n::languageLinkTags() ?>
    <?php $this->head(); echo "\n" ?>
  </head>
  <?= Html::beginTag('body', [
    'itemprop' => true,
    'proptype' => 'http://schema.org/WebPage',
    'data' => [
      'theme' => Yii::$app->theme->theme,
    ],
    'class' => [
      Yii::$app->theme->isDarkTheme ? 'theme-dark' : 'theme-light',
      $isApple ? 'apple' : 'not-apple',
    ],
  ]) . "\n" ?>
    <?php $this->beginBody() ?><?= "\n" ?>
<?php if (!$isPjax) { ?>
      <header>
        <?= $this->render('/layouts/testsite') . "\n" ?>
        <?= $this->render('/layouts/navbar') . "\n" ?>
      </header>
<?php } ?>
      <main>
        <?= $content ?><?= "\n" ?>
      </main>
<?php if (!$isPjax) { ?>
      <?= $this->render('/layouts/footer') ?><?= "\n" ?>
<?php if (!Yii::$app->user->isGuest) { ?>
        <?= $this->render('/includes/battle-input-modal-2') . "\n" ?>
<?php } ?>
<?php } ?>
      <span id="event"></span>
<?php if (!$isPjax) { ?>
      <?= ColorSchemeDialog::widget([
        'id' => 'color-scheme-dialog',
      ]) . "\n" ?>
      <?= LanguageDialog::widget([
        'id' => 'language-dialog',
      ]) . "\n" ?>
      <?= TimezoneDialog::widget([
        'id' => 'timezone-dialog',
      ]) . "\n" ?>
      <?= CookieAlert::widget() . "\n" ?>
<?php } ?>
    <?php $this->endBody() ?><?= "\n" ?>
  </body>
</html>
<?php $this->endPage() ?>
