<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\UserAuthKey;
use yii\helpers\Html;
use yii\helpers\Url;

$title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Privacy Policy'),
]);
$this->context->layout = 'main';
$this->title = $title;
?>
<div class="container">
  <h1>
    <?= Html::encode(Yii::t('app', 'Privacy Policy')) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p>
    <?= Html::encode(Yii::t('app', 'This website ({0}) collects the following data:', [Yii::$app->name])) . "\n" ?>
  </p>
  <ul>
<?php $list = [
  'Access time',
  'IP address',
  'The address of the web site that linked here (aka "referer")',
  'Your OS, browser name, and version that you are using (aka "user agent")',
] ?>
<?php foreach ($list as $_) { ?>
    <?= Html::tag('li', Html::encode(Yii::t('app', $_))) . "\n" ?>
<?php } ?>
  </ul>
  <p><?= Html::encode(
    Yii::t('app', 'This website uses cookies to track your session or save your configuration (e.g., language / time zone settings).')
  ) ?></p>
  <p>
    <?= Html::encode(Yii::t('app', 'We don\'t release your collected information, like your IP address. However, statistical information will be released.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app', 'If there is an investigation being conducted by the police or other authority, your information will be released.')) . "\n" ?>
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
              <li><?= Html::encode(Yii::t('app-cookie', 'A token to avoid CSRF vulnerability')) ?></li>
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
<?php $cookies = [
    'eu_cn' => 365 * 86400,
    'external_referer' => 7 * 86400,
    'guest_id' => 365 * 2 * 86400,
    'personalization_id' => 365 * 2 * 86400,
    'tfw_exp' => 14 * 86400,
] ?>
<?php foreach ($cookies as $cookieName => $duration) { ?>
        <tr>
          <td><?= Html::encode(Yii::t('app-cookie', '(Twitter)')) ?></td>
          <td><code><?= Html::encode($cookieName) ?></code></td>
          <td><?= Html::encode(Yii::$app->formatter->asDuration($duration, ' ')) ?></td>
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
<?php } ?>
      </tbody>
    </table>
  </div>

  <h2 id="image">
    <?= Html::encode(Yii::t('app', 'About image sharing with the IkaLog team')) . "\n" ?>
  </h2>
  <p>
    <?= Html::encode(Yii::t('app', 'Your uploaded data (battle stats, images, and modification history) will be shared with the IkaLog development team.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app', 'This is done automatically and the data will not be deleted even if the the battle is deleted.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app', 'This behavior was started on 27 Oct 2015.')) . "\n" ?>
  </p>
</div>
