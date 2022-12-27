<?php

use app\components\Version;
use app\components\widgets\Icon;
use yii\helpers\Html;

$params = Yii::$app->params;

$ver = Yii::$app->version;
$revL = $params['gitRevision']['longHash'] ?? null;
$revS = $params['gitRevision']['shortHash'] ?? null;
if ($tmp = ($params['gitRevision']['lastCommitted'] ?? null)) {
    $committed = (new DateTimeImmutable($tmp))->setTimeZone(new DateTimeZone(Yii::$app->timeZone));
} else {
    $committed = null;
}
?>
<footer class="footer">
  <div class="container text-muted">
    <div class="footer-version">
      <?= Html::encode(Yii::$app->name) . "\n" ?>
      <?= implode(', ', array_filter([
        sprintf(
          'Version %s',
          Html::encode($ver)
        ),
        ($revL && $revS)
          ? sprintf(
            'Revision %s',
            Html::a(
              Html::encode($revS),
              'https://github.com/fetus-hina/stat.ink/tree/' . $revL
            )
          )
          : null,
        $committed
          ? Html::tag(
            'time',
            Html::encode(
              Yii::$app->formatter->asRelativeTime(
                $committed,
                (int)($_SERVER['REQUEST_TIME'] ?? time())
              )
            ),
            [
              'datetime' => $committed->setTimeZone(new DateTimeZone('Etc/UTC'))->format(DateTime::ATOM),
            ]
          )
          : null
      ])) . "\n" ?>
    </div>
    <div class="footer-author">
      <?= implode(' ', [
        Html::encode(sprintf(
          'Copyright © 2015-%d AIZAWA Hina.',
          $committed ? $committed->format('Y') : 2017
        )),
        Html::a(
          Icon::twitter(),
          'https://twitter.com/fetus_hina',
          [
            'title' => 'Twitter: fetus_hina',
            'class' => 'auto-tooltip',
          ]
        ),
        Html::a(
          Icon::github(),
          'https://github.com/fetus-hina',
          [
            'title' => 'GitHub: fetus-hina',
            'class' => 'auto-tooltip',
          ]
        ),
      ]) . "\n" ?>
    </div>
    <div class="footer-nav">
      <?= implode(' | ', [
        Html::a(
          Html::encode(Yii::t('app', 'API (Splatoon 2)')),
          'https://github.com/fetus-hina/stat.ink/tree/master/doc/api-2'
        ),
        Html::a(
          Html::encode(Yii::t('app', 'API (Splatoon)')),
          ['/site/api']
        ),
        Html::a(
          Html::encode(Yii::t('app-privacy', 'Privacy Policy')),
          ['/site/privacy']
        ),
        Html::a(
          Html::encode(Yii::t('app', 'Open Source Licenses')),
          ['/site/license']
        ),
      ]) . "\n" ?>
    </div>
    <div class="footer-notice">
      <?= implode('<br>', [
        Html::encode(
          Yii::t('app', 'This website is an UNOFFICIAL SERVICE. It is not related to the Splatoon development team or Nintendo.')
        ),
        implode(' ', [
          Html::encode(
            Yii::t('app', 'This website is an open source project. It is under the MIT License. The source code is available on GitHub.')
          ),
          Html::a(
            Icon::github(),
            'https://github.com/fetus-hina/stat.ink'
          ),
        ]),
        implode(' ', [
          Html::encode(
            Yii::t('app', 'Feedback or suggestions are welcome. Please contact me via GitHub or Twitter.')
          ),
          Html::a(
            Icon::github(),
            'https://github.com/fetus-hina/stat.ink'
          ),
          Html::a(
            Icon::twitter(),
            'https://twitter.com/fetus_hina'
          ),
        ]),
      ]) . "\n" ?>
    </div>
    <div class="footer-powered">
      <?= sprintf(
        '%s %s, %s<br>',
        Html::encode(Yii::t('app', 'Powered by')),
        Html::a(
          Html::encode('Yii Framework ' . Yii::getVersion()),
          'http://www.yiiframework.com/'
        ),
        Html::a(
          Html::encode('PHP ' . phpversion()),
          'http://php.net/'
        )
      ) . "\n" ?>
      <?= sprintf(
        '%s %s.',
        Html::encode(Yii::t('app', 'Served by')),
        Html::encode(php_uname('n'))
      ) . "\n" ?>
    </div>
  </div>
</footer>
