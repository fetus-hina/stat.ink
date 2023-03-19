<?php

declare(strict_types=1);

use app\assets\InlineListAsset;
use app\components\Version;
use app\components\widgets\Icon;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

InlineListAsset::register($this);

$params = Yii::$app->params;

$ver = Yii::$app->version;
$revL = $params['gitRevision']['longHash'] ?? null;
$revS = $params['gitRevision']['shortHash'] ?? null;
if ($tmp = ($params['gitRevision']['lastCommitted'] ?? null)) {
  $committed = (new DateTimeImmutable($tmp))->setTimeZone(new DateTimeZone(Yii::$app->timeZone));
} else {
  $committed = null;
}

$discordInviteCode = ArrayHelper::getValue($params, 'discordInviteCode');
if (!is_string($discordInviteCode)) {
  $discordInviteCode = null;
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
      <?= Html::tag(
        'ul',
        implode(
          '',
          array_map(
            fn (?string $html): ?string => is_string($html) ? Html::tag('li', $html) : null,
            array_filter(
              [
                Html::a(
                  Html::encode(Yii::t('app', 'API (Splatoon 3)')),
                  'https://github.com/fetus-hina/stat.ink/wiki/Spl3-API:-Battle-%EF%BC%8D-Post',
                  ['target' => '_blank', 'rel' => 'noopener'],
                ),
                Html::a(
                  Html::encode(Yii::t('app', 'API (Splatoon 2)')),
                  'https://github.com/fetus-hina/stat.ink/tree/master/doc/api-2',
                  ['target' => '_blank', 'rel' => 'noopener'],
                ),
                Html::a(
                  Html::encode(Yii::t('app', 'API (Splatoon)')),
                  ['/site/api'],
                  ['target' => '_blank', 'rel' => 'noopener'],
                ),
                Html::a(
                  Html::encode(Yii::t('app-privacy', 'Privacy Policy')),
                  ['/site/privacy']
                ),
                Html::a(
                  Html::encode(Yii::t('app', 'Open Source Licenses')),
                  ['/site/license']
                ),
                $discordInviteCode
                  ? Html::a(
                    Html::img(
                      'https://img.shields.io/badge/Discord-%235865F2.svg?style=for-the-badge&logo=discord&logoColor=white',
                      [
                        'alt' => 'Discord',
                        'class' => 'auto-tooltip',
                        'height' => (string)round(28 * 0.5),
                        'title' => Yii::t('app', '{siteName} Discord Community', ['siteName' => Yii::$app->name]),
                        'width' => (string)round(104 * 0.5),
                      ],
                    ),
                    sprintf('https://discord.gg/%s', rawurlencode($discordInviteCode)),
                    ['target' => '_blank', 'rel' => 'nofollow noopener'],
                  )
                  : null,
              ],
              fn (?string $v): bool => $v !== null,
            ),
          ),
        ),
        ['class' => 'inline-list'],
      ) . "\n" ?>
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
