<?php

declare(strict_types=1);

use app\assets\AboutAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Getting Started'),
]);
$this->title = $title;
$this->context->layout = 'main';

$aboutAsset = AboutAsset::register($this);
?>
<div class="container">
  <h1>
    Getting Started
  </h1>
  <p>
    This website collects your Splatoon logs, and analyzes them.
  </p>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <h2>
    How do I make my Splat Log?
  </h2>
  <p>
    There are two ways to make your Splatoon play log. The first option is having a program like Ikalog do it automatically, and the another option is entering the data manually.
  </p>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <h3>
        Automatic Mode (Recommended)
      </h3>
      <p>
        Use a Splatoon data collection program, such as <a href="https://github.com/hasegaw/IkaLog/wiki/en_Home">IkaLog</a>, to analyze your gameplay.
      </p>
      <p>
        IkaLog analyzes your gameplay by monitoring the video output from Wii U console. The diagram below describes how it all works:
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
      <p>
        If the application is set up correctly, all of your Splatoon gameplay will be analyzed, and helpful data will be submitted to your stat.ink account automatically.&#32;
        You can benefit from all of stat.ink's features in this case.
      </p>
      <p>
        If you don't already have an HDMI capture card, like an AverMedia or Elgato, you will need to get one.
      </p>
      <ul>
        <li>
          Some HDMI capture devices (e.g. AverMedia AVT-C875 and Intensity Shuttle) have a built in HDMI splitter.&#32;
          In this case, you won't need to buy a splitter separately.&#32;
          Otherwise, you will need a splitter so you can connect the Wii U's output to both your TV and the capture device.
        </li>
        <li>
          720p must be supported by the capture device.
        </li>
        <li>
          IkaLog doesn't work with some HDMI captures device, due to compatibility issues (e.g. inaccurate/poor image quality or incompatible driver software).&#32;
          It is strongly suggested to check the <a href="https://github.com/hasegaw/IkaLog/wiki/en_CaptureDevices">"reported HDMI devices" list on the IkaLog wiki</a> if you are going to buy one.&#32;
          Elgato capture cards will not work directly with IkaLog, so if you have one, you'll have to use the screen capture method. <!--detailed on the GitHub wiki below.-->
        </li>
      </ul>
      <hr>
      <p>
        List of data collection software that work with stat.ink automatically:
      </p>
      <ul>
        <li>
          <a href="https://github.com/hasegaw/IkaLog/wiki/en_Home">IkaLog / WinIkaLog</a> (Windows, Mac, Linux)
        </li>
      </ul>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <h3>
        Manual Mode
      </h3>
      <p>
        You can also submit your battle results from applications such as "<a href="https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec">IkaRec</a>."
      </p>
      <p>
        It's quite simple; you can input the battle results manually.&#32;
        Since you have oly a few seconds to view the scoreboard, you are able to fill a few details about the match (e.g. stage, mode, your weapon, and win/defeat).
      </p>
      <hr>
      <p>
        List of data collection software work with stat.ink in manual mode:
      </p>
      <ul>
        <li>
          <a href="https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec">IkaRec</a> (means Squid-Recorder, for Android devices)
          <ul>
            <li>
              <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec">Japanese version of IkaRec</a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>

  <hr>
  <h2>
    Okay, now how do I use it?
  </h2>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <h3>
        Using IkaLog
      </h3>
      <p>
        SquidBoards has good documentation. Check it out:<br>
        <a href="http://squidboards.com/guides/how-to-set-up-ikalog-and-stat-ink-for-battle-result-tracking.217/">How to set up IkaLog and stat.ink for battle result tracking!</a>
      </p>
      <p>
        <a href="https://github.com/hasegaw/IkaLog/wiki/en_WinIkaLog">The IkaLog GitHub wiki</a> also has instructions for setting IkaLog up with stat.ink.
      </p>
      <p>
        You will need to configure IkaLog with your video capture device.&#32;
        Refer to the IkaLog documentation for instructions on how to do so.
      </p>
      <ol>
        <li>
          <?= Html::a('Register an account on stat.ink.', ['user/register']) . "\n" ?>
          If you're already registed, log in to your stat.ink account.
        </li>
        <li>
          Open "Your Name" → "Settings" in menu bar.
        </li>
        <li>
          Show your API Key by clicking eye icon.&#32;
          API Key will be something like <code>ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefg</code>.<br>
          The API Key is like a password for you.&#32;
          <strong>Never share the key with anyone!</strong><br>
          If you have to take a screenshot of this page for any reason, <strong>remove your API Key or cover it with a rectangle</strong> in an image editor.
        </li>
        <li>
          Open WinIkaLog application, click "Configure" button, and select "stat.ink" tab.
        </li>
        <li>
          Copy and Paste your API Key into the API Key field in IkaLog window.
        </li>
        <li>
          Check the "☑ Submit to stat.ink" checkbox.
        </li>
        <li>
          Click the "Apply" button to reflect the settings.
        </li>
      </ol>
      <p>
        Once the configuration is done, your battle results will be submitted to stat.ink automatically.
      </p>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <h3>
        Using IkaRec
      </h3>
      <p>
        The English version of IkaRec can be found <a href="https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec">here</a>.
      </p>
      <p>
        Please see <a href="https://www.reddit.com/r/splatoon/comments/4vqg5r/english_ikarec_android_app_release/">this reddit post</a> for more information regarding how to use the app.
      </p>
    </div>
  </div>
  <hr>
  <h2>
    For software developers
  </h2>
  <p>
    <a href="https://github.com/fetus-hina/stat.ink/blob/master/API.md">stat.ink provides an API to the public.</a> You can design your own software if you want.
  </p>
  <p>
    You can use your application yourself, but others might find it useful too, so consider sharing.
  </p>
  <hr>
  <p>
    This website was developed by AIZAWA Hina &lt;hina@fetus.jp&gt; (<span class="fab fa-twitter left"></span>fetus_hina, <span class="fab fa-github left"></span>fetus-hina) as a personal project.&#32;
    This project was not produced in association with Nintendo.&#32;
    Don't ask them about stat.ink or IkaLog, they won't know anything.
  </p>
  <p>
    The source code of stat.ink is published as open source, under MIT License. 
  </p>
</div>
