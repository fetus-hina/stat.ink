<?php
use app\assets\AppAsset;
use app\assets\BootstrapNotifyAsset;
use app\assets\BootswatchAsset;
use app\components\helpers\I18n;
use app\components\widgets\ColorSchemeDialog;
use app\components\widgets\LanguageDialog;
use app\components\widgets\TimezoneDialog;
use cybercog\yii\googleanalytics\widgets\GATracking;
use yii\helpers\Html;
use yii\helpers\Json;

AppAsset::register($this);
Yii::$app->theme->registerAssets($this);

// $bootswatch = BootswatchAsset::register($this);
// $bootswatch->theme = 'darkly';

$_flashes = Yii::$app->getSession()->getAllFlashes();
if ($_flashes) {
  $_hashKey = microtime(false);
  foreach ($_flashes as $_key => $_messages) {
    if (is_array($_messages)) {
      $i = 0;
      foreach ($_messages as $_message) {
        $this->registerJs(
          sprintf(
            '(function($){$.notify(%s)})(jQuery);',
            Json::encode([
              'message' => Html::encode($_message),
              'type' => Html::encode($_key),
            ])
          ),
          hash_hmac('md5', $_hashKey, (string)($i++))
        );
      }
    } else {
      $this->registerJs(
        sprintf(
          '(function($){$.notify(%s,%s)})(jQuery);',
          Json::encode([
            'message' => Html::encode($_messages),
          ]),
          Json::encode([
            'type' => Html::encode($_key),
            'z_index' => 11031,
          ])
        )
      );
    }
  }
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<?= Html::beginTag('html', ['lang' => Yii::$app->language]) . "\n" ?>
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no,email=no,address=no">
    <?= Html::csrfMetaTags() ?>
    <?= Html::tag(
      'title',
      Html::encode(trim($this->title) == '' ? Yii::$app->name : $this->title)
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
  ]) . "\n" ?>
    <?php $this->beginBody() ?><?= "\n" ?>
      <header>
        <?= $this->render('/layouts/navbar') ?><?= "\n" ?>
      </header>
      <main>
        <?= $content ?><?= "\n" ?>
      </main>
      <?= $this->render('/layouts/footer') ?><?= "\n" ?>
<?php if (!Yii::$app->user->isGuest) { ?>
        <?= $this->render('/includes/battle-input-modal-2') . "\n" ?>
<?php } ?>
      <span id="event"></span>
<?php if (Yii::$app->params['googleAnalytics'] ?? null) { ?>
        <?= GATracking::widget([
          'trackingId' => Yii::$app->params['googleAnalytics'],
        ]) . "\n" ?>
<?php } ?>
      <?= ColorSchemeDialog::widget([
        'id' => 'color-scheme-dialog',
      ]) . "\n" ?>
      <?= LanguageDialog::widget([
        'id' => 'language-dialog',
      ]) . "\n" ?>
      <?= TimezoneDialog::widget([
        'id' => 'timezone-dialog',
      ]) . "\n" ?>
    <?php $this->endBody() ?><?= "\n" ?>
  </body>
</html>
<?php $this->endPage() ?>
