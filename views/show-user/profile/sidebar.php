<?php

declare(strict_types=1);

use app\components\widgets\UserIcon;
use app\models\User;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var User $user
 */

if (Yii::$app->request->isPjax) {
  echo '<div id="person-box" class="col-xs-12 col-md-3"></div>';
  return;
}

$css = [
  '#person-box h1' => [
    'font-size' => '30px',
    'margin' => '15px 0 5px',
    'font-weight' => '600',
  ],
  '#person-box h2' => [
    'font-weight' => '300',
    'font-size' => '24px',
    'margin' => 0,
  ],
];
$this->registerCss(implode('', array_map(
  function ($key, $value) {
      return sprintf(
      '%s{%s}',
      $key,
      Html::cssStyleFromArray($value)
    );
  },
  array_keys($css),
  array_values($css)
)));
?>
<div id="person-box" class="col-xs-12 col-md-3" itemscope itemtype="http://schema.org/Person">
  <?= UserIcon::widget([
    'user' => $user,
    'options' => [
      'class' => [
        'img-responsive',
        'img-thumbnail',
        'img-rounded',
        'w-100',
      ],
    ],
  ]) . "\n" ?>
  <h1 itemprop="name">
    <?= Html::encode($user->name) . "\n" ?>
  </h1>
  <h2 itemprop="alternateName">
    <?= Html::encode('@' . $user->screen_name) . "\n" ?>
  </h2>
<?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $user->id) { ?>
  <div class="text-right">
    <?= Html::a(
      Yii::t('app', 'Edit'),
      ['/user/profile'],
      ['class' => 'btn btn-default']
    ) . "\n" ?>
  </div>
<?php } ?>
  <hr>
  <?= $this->render('//show-user/profile/links', ['user' => $user]) . "\n" ?>
</div>
