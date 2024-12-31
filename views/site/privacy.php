<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\web\Application;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\UserAuthKey;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\View;

/**
 * @var View $this
 */

assert($this->context instanceof Controller);

$title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app-privacy', 'Privacy Policy'),
]);
$this->context->layout = 'main';
$this->title = $title;
?>
<div class="container">
  <h1>
    <?= Html::encode(Yii::t('app-privacy', 'Privacy Policy')) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p>
    <?= Html::encode(Yii::t('app-privacy', 'This website ({0}) collects the following data:', [Yii::$app->name])) . "\n" ?>
  </p>
  <ul>
<?php $list = [
  'Access time',
  'IP address',
  'The address of the web site that linked here ("referrer" or "referer")',
  'Your OS, browser name, and version that you are using ("user agent")',
] ?>
<?php foreach ($list as $_) { ?>
    <?= Html::tag('li', Html::encode(Yii::t('app-privacy', $_))) . "\n" ?>
<?php } ?>
  </ul>
  <p><?= Html::encode(
    Yii::t('app-privacy', 'This website uses cookies to track your session or save your configuration (e.g., language / time zone settings).')
  ) ?></p>
  <p>
    <?= Html::encode(Yii::t('app-privacy', 'We don\'t release your collected information, like your IP address. However, statistical information will be released.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app-privacy', 'If there is an investigation being conducted by the police or other authority, your information will be released.')) . "\n" ?>
  </p>

  <h2 id="cookie">
    <?= Html::encode(Yii::t('app-cookie', 'Cookies')) . "\n" ?>
  </h2>
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th><?= Html::encode(Yii::t('app-cookie', 'Origin')) ?></th>
          <th><?= Html::encode(Yii::t('app-cookie', 'Cookie ID (Name)')) ?></th>
          <th><?= Html::encode(Yii::t('app-cookie', 'Expires')) ?></th>
          <th><?= Html::encode(Yii::t('app-cookie', 'What to use')) ?></th>
        </tr>
      </thead>
      <tbody>
<?php $myOrigin = preg_replace('#^.*?://([^/]+).*#', '$1', Url::home(true)) ?>
        <tr>
          <td><?= Html::encode($myOrigin) ?></td>
          <td><code><?= Html::encode(Yii::$app->session->name) ?></code></td>
          <td><?= Html::encode(Yii::t('app-cookie', '(Session)')) ?></td>
          <td>
            <ul>
              <li><?= Html::encode(Yii::t('app-cookie', 'Track your login status')) ?></li>
              <li><?= Html::encode(Yii::t('app-cookie', 'Keep your input data while verifying email address')) ?></li>
            </ul>
          </td>
        </tr>
<?php if (Yii::$app->request->enableCsrfValidation && Yii::$app->request->enableCsrfCookie) { ?>
        <tr>
          <td><?= Html::encode($myOrigin) ?></td>
          <td><code><?= Html::encode(Yii::$app->request->csrfParam) ?></code></td>
          <td><?= Html::encode(Yii::t('app-cookie', '(Session)')) ?></td>
          <td>
            <ul>
              <li><?= Html::encode(Yii::t('app-cookie', 'A token used to avoid CSRF vulnerability')) ?></li>
            </ul>
          </td>
        </tr>
<?php } ?>
<?php if (Yii::$app->user->enableAutoLogin && Yii::$app->user->identityCookie) { ?>
        <tr>
          <td><?= Html::encode($myOrigin) ?></td>
          <td><code><?= Html::encode(Yii::$app->user->identityCookie['name']) ?></code></td>
          <td><?= Html::encode(Yii::$app->formatter->asDuration(UserAuthKey::VALID_PERIOD)) ?></td>
          <td>
            <ul>
              <li><?= Html::encode(Yii::t('app-cookie', 'A token used for the auto login feature')) ?></li>
            </ul>
          </td>
        </tr>
<?php } ?>
        <tr>
          <td><?= Html::encode($myOrigin) ?></td>
          <td><code>language</code></td>
          <td><?= Html::encode(Yii::$app->formatter->asDuration(86400 * 366, ' ')) ?></td>
          <td>
            <ul>
              <li><?= Html::encode(Yii::t('app-cookie', 'Saving the specified or automatically detected language setting')) ?></li>
            </ul>
          </td>
        </tr>
        <tr>
          <td><?= Html::encode($myOrigin) ?></td>
          <td><code><?= Html::encode(Application::COOKIE_MACHINE_TRANSLATION) ?></code></td>
          <td><?= Html::encode(Yii::$app->formatter->asDuration(86400 * 366, ' ')) ?></td>
          <td>
            <ul>
              <li><?= Html::encode(Yii::t('app-cookie', 'Saving "Enable machine-translation" option state')) ?></li>
            </ul>
          </td>
        </tr>
        <tr>
          <td><?= Html::encode($myOrigin) ?></td>
          <td><code>theme</code></td>
          <td><?= Html::encode(Yii::$app->formatter->asDuration(86400 * 366, ' ')) ?></td>
          <td>
            <ul>
              <li><?= Html::encode(Yii::t('app-cookie', 'Saving the specified or default theme (color scheme) setting')) ?></li>
            </ul>
          </td>
        </tr>
        <tr>
          <td><?= Html::encode($myOrigin) ?></td>
          <td><code>timezone</code></td>
          <td><?= Html::encode(Yii::$app->formatter->asDuration(86400 * 366, ' ')) ?></td>
          <td>
            <ul>
              <li><?= Html::encode(Yii::t('app-cookie', 'Saving the specified or automatically detected time zone setting')) ?></li>
            </ul>
          </td>
        </tr>
        <tr>
          <td><?= Html::encode('.stat.ink') ?></td>
          <td><?= implode('<br>', array_map(
            function (string $name): string {
              return Html::tag('code', Html::encode($name));
            },
            [
              '_cf_bm',
              '_cfduid',
              '_cflb',
              'cf_ob_info',
              'cf_use_ob',
            ]
          )) ?></td>
          <td><?= Html::encode(Yii::t('app-cookie', '(3rd party defined)')) ?></td>
          <td>
            <ul>
              <li>
                <?= Html::encode(Yii::t('app-cookie', 'Issued and used by CloudFlare')) ?><br>
                <?= Yii::t(
                  'app-cookie',
                  'Visit their {description} and/or {privacy} for more details',
                  [
                      'description' => Html::a(
                          Html::encode(Yii::t('app-cookie', 'descriptions')),
                          'https://support.cloudflare.com/hc/en-us/articles/200170156-Understanding-the-Cloudflare-Cookies',
                          ['rel' => 'nofollow external']
                      ),
                      'privacy' => Html::a(
                          Html::encode(Yii::t('app-cookie', 'privacy policy')),
                          'https://www.cloudflare.com/privacypolicy/',
                          ['rel' => 'nofollow external']
                      ),
                  ]
                ) . "\n" ?>
              </li>
            </ul>
          </td>
        </tr>
        <tr>
          <td><?= Html::encode(Yii::t('app-cookie', '(Twitter)')) ?></td>
          <td><?= implode('<br>', array_map(
            function (string $name): string {
              return Html::tag('code', Html::encode($name));
            },
            [
              'eu_cn',
              'external_referer',
              'guest_id',
              'personalization_id',
              'tfw_exp',
            ]
          )) ?></td>
          <td><?= Html::encode(Yii::t('app-cookie', '(3rd party defined)')) ?></td>
          <td>
            <ul>
              <li><?= Html::encode(Yii::t('app-cookie', 'Issued and used by Twitter')) ?></li>
              <li><?= Html::a(
                Html::encode(Yii::t('app-cookie', 'Their privacy policy')),
                'https://twitter.com/en/privacy',
                ['rel' => 'external']
              ) ?></li>
            </ul>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <h2 id="image">
    <?= Html::encode(Yii::t('app-privacy', 'About image sharing with the IkaLog team')) . "\n" ?>
  </h2>
  <p>
    <?= Html::encode(Yii::t('app-privacy', 'Your uploaded data (battle stats, images, and modification history) will be shared with the IkaLog development team.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app-privacy', 'This is done automatically and the data will not be deleted even if the the battle is deleted.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app-privacy', 'This behavior was started on 27 Oct 2015.')) . "\n" ?>
  </p>

  <h2 id="location"><?= Html::encode(Yii::t('app-privacy', 'Locations and Law')) ?></h2>
  <h3><?= Html::encode(Yii::t('app-privacy', 'Server Location')) ?></h3>
  <p><?= Html::encode(Yii::t('app-privacy', 'Our servers are located in Ishikari Datacenter (Hokkaido, Japan), SAKURA internet inc.')) ?></p>
  <p><?= Html::encode(Yii::t('app-privacy', 'SAKURA internet inc. is our sponsor.')) ?></p>
  <p><?= Html::encode(Yii::t('app-privacy', 'They never have access to your private data.')) ?></p>

  <h3><?= Html::encode(Yii::t('app-privacy', 'Author')) ?></h3>
  <div class="table-responsive">
    <table class="table table-striped w-auto">
      <tbody>
        <tr>
          <th scope="row"><?= Html::encode(Yii::t('app-privacy', 'Handle Name')) ?></th>
          <td>
            <?= Html::encode(Yii::t('app-privacy', 'AIZAWA Hina (相沢 陽菜)')) . "\n" ?>
            <?= Html::encode(Yii::t('app-privacy', '(It is not my real name)')) . "\n" ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><?= Html::encode(Yii::t('app-privacy', 'Address')) ?></th>
          <td><?= Html::encode(Yii::t('app-privacy', 'Osaka, Japan')) ?></td>
        </tr>
        <tr>
          <th scope="row"><?= Html::encode(Yii::t('app-privacy', 'SNS')) ?></th>
          <td>
            <?= Html::a('GitHub', 'https://github.com/fetus-hina/stat.ink') . "\n" ?>
            <?= Html::a('Twitter', 'https://twitter.com/fetus_hina') . "\n" ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><?= Html::encode(Yii::t('app-privacy', 'PGP Key')) ?></th>
          <td>
            <?= Html::a('F6B887CD', 'https://pgp.mit.edu/pks/lookup?op=get&search=0x26CF8461F6B887CD') . "\n" ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <h3><?= Html::encode(Yii::t('app-privacy', 'Law')) ?></h3>
  <p><?= Html::encode(Yii::t('app-privacy', 'We are governed by Japanese law.')) ?></p>
  <p><?= Html::encode(Yii::t('app-privacy', 'The parties hereby consent to and confer exclusive jurisdiction upon Osaka District Court or Hirakata Summary Court.')) ?></p>
</div>
