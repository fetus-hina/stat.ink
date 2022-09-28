<?php

declare(strict_types=1);

use app\components\widgets\GameVersionIcon;
use app\models\User;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);

$this->context->layout = 'main';
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->userIcon->absUrl ?? $user->jdenticonPngUrl]);
if ($user->twitter != '') {
    $this->registerMetaTag(['name' => 'twitter:creator', 'content' => sprintf('@%s', $user->twitter)]);
}

?>
<div id="profile" class="container">
  <div class="row">
    <?= $this->render('//show-user/profile/sidebar', ['user' => $user]) . "\n" ?>
    <div class="col-xs-12 col-md-9">
      <?= Tabs::widget([
        'items' => [
          [
            'encode' => false,
            'label' => implode('', [
              Html::tag(
                'span',
                GameVersionIcon::widget(['version' => 3]),
                [
                  'title' => Yii::t('app', 'Splatoon 3'),
                  'class' => 'auto-tooltip',
                ]
              ),
              Html::tag('span', Yii::t('app', 'Splatoon 3'), ['class' => 'sr-only']),
            ]),
            'active' => true,
            'content' => $this->render('//show-user/profile/splatoon3', ['user' => $user]),
          ],
          [
            'encode' => false,
            'label' => implode('', [
              Html::tag(
                'span',
                GameVersionIcon::widget(['version' => 2]),
                [
                  'title' => Yii::t('app', 'Splatoon 2'),
                  'class' => 'auto-tooltip',
                ]
              ),
              Html::tag('span', Yii::t('app', 'Splatoon 2'), ['class' => 'sr-only']),
            ]),
            'active' => false,
            'content' => $this->render('//show-user/profile/splatoon2', ['user' => $user]),
          ],
          [
            'encode' => false,
            'label' => implode('', [
              Html::tag(
                'span',
                GameVersionIcon::widget(['version' => 1]),
                [
                  'title' => Yii::t('app', 'Splatoon'),
                  'class' => 'auto-tooltip',
                ]
              ),
              Html::tag('span', Yii::t('app', 'Splatoon'), ['class' => 'sr-only']),
            ]),
            'active' => false,
            'content' => $this->render('//show-user/profile/splatoon', ['user' => $user]),
          ],
        ],
      ]) . "\n" ?>
      <div class="row">
        <div class="col-xs-12" id="activity">
          <?= $this->render('//show-user/profile/activity', ['user' => $user]) . "\n" ?>
        </div>
      </div>
    </div>
  </div>
</div>
