<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author CS <1265370+ccl13@users.noreply.github.com>
 */

declare(strict_types=1);

use app\assets\AboutAsset;
use app\assets\AppLinkAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$title = implode(' | ', [
    Yii::$app->name,
    Yii::t('app', 'Getting Started'),
]);
$this->context->layout = 'main';

$aboutAsset = AboutAsset::register($this);
$iconAsset = AppLinkAsset::register($this);
?>
<div class="container">
  <h1>
    About <?= Html::encode(Yii::$app->name) . "\n" ?>
  </h1>
  <p>
    This site collects, stores, and analyzes results data from the Splatoon series.
  </p>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <h2>
    How to register your results
  </h2>
  <p>
    Currently, there are 3 main ways to register your data.
  </p>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-3">
      <h3>
        1-a. Using data from SplatNet 3
      </h3>
      <p>
        There are several applications that use the private API of SplatNet 3 (Nintendo Switch Online) to retrieve data.
      </p>
      <h4>
        <?= Html::a(
          Html::encode('s3s'),
          'https://github.com/frozenpandaman/s3s',
          ['target' => '_blank'],
        ) . "\n"?>
      </h4>
      <p>
        s3s is an application that runs on a PC or server.
      </p>
      <p>
        Python 3 runtime environment is required.
      </p>
      <p>
        See the s3s GitHub for instructions on how to set it up.
      </p>
      <hr>
      <h4>
        <?= Html::a(
          Html::encode('s3si.ts'),
          'https://github.com/spacemeowx2/s3si.ts',
          ['target' => '_blank'],
        ) . "\n" ?>
      </h4>
      <p>
        s3si.ts is an application that runs on a PC or server.
      </p>
      <p>
        TypeScript runtime environment (Deno) is required.
      </p>
      <hr>
      <?= Html::tag('iframe', '', [
        'allow' => 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
        'allowfullscreen' => true,
        'frameborder' => '0',
        'height' => '315',
        'referrerpolicy' => 'strict-origin-when-cross-origin',
        'src' => 'https://www.youtube.com/embed/CEzU06UcAGw',
        'title' => 'YouTube video player',
        'width' => '560',
        'style' => [
          'max-width' => '100%',
        ],
      ]) . "\n" ?>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-3">
      <h3>
        1-b. Using data from SplatNet 2
      </h3>
      <p>
        There are several applications that use the private API of SplatNet 2 (Nintendo Switch Online) to retrieve data.
      </p>
      <h4>
        <?= Html::a(
          Html::encode('splatnet2statink'),
          'https://github.com/frozenpandaman/splatnet2statink',
          ['target' => '_blank'],
        ) . "\n" ?>
      </h4>
      <p>
        splatnet2statink is an application that runs on a PC or server.
      </p>
      <p>
        Python 3 runtime environment is required.
      </p>
      <hr>
      <h4>
        <?= Html::a(
          implode('', [
            $iconAsset->squidTracks,
            Html::encode('SquidTracks'),
          ]),
          'https://github.com/hymm/squid-tracks',
          ['target' => '_blank'],
        ) . "\n" ?>
      </h4>
      <p>
        SquidTracks is an application that runs on a PC.
      </p>
      <p>
        Do not use this app as it is not compatible with the current game data.
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-3">
      <h3>
        2. Analyze screen image (Splatoon 1)
      </h3>
      <p class="alert alert-danger">
        Currently, a properly working IkaLog for Splatoon 2 &amp; 3 has not been released.
      </p>
      <p>
        This method uses <a href="https://github.com/hasegaw/IkaLog/wiki/en_WinIkaLog">IkaLog</a> or
        other compatible software to analyze the screen and automatically register the results.
      </p>
      <p>
        In the case of IkaLog, play data is analyzed by inputting a copy of the video signal output
        from the Switch/Wii U to the TV to the PC.
      </p>
      <?= Html::img(
        Yii::$app->assetManager->getAssetUrl($aboutAsset, 'overview.en.png'),
        [
          'alt' => '',
          'title' => '',
          'style' => [
            'width' => '100%',
            'max-width' => '530px',
          ],
        ]
      ) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-3">
      <h3>
        3. Manually
      </h3>
      <p>
        This is a method of manually registering your results using compatible software, such as
        <a href="https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec">IkaRec</a>.
      </p>
      <p>
        This method is quite simple: the user manually registers based on the results screen
        displayed on the screen.
      </p>
      <p>
        Because of the limited time available for registration, only minimal information would be
        recorded.
      </p>
      <p>
        There are no practical applications available for Splatoon 2 or Splatoon 3.
      </p>
    </div>
  </div>

  <h2>
    Advanced Usage
  </h2>
  <p>
    We expose our API so you can create your own software.
  </p>

  <hr>

  <p>
    This website was developed by AIZAWA Hina &lt;hina@fetus.jp&gt; (<?= Icon::twitter() ?> fetus_hina, <?= Icon::github() ?> fetus-hina) as a personal project.&#32;
    This project was not produced in association with Nintendo.&#32;
    Don't ask them about stat.ink or IkaLog, they won't know anything.
   </p>
   <p>
    The source code of stat.ink is published as open source, under MIT License.
  </p>
</div>
