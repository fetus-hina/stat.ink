<?php
use app\assets\AppLinkAsset;
use app\assets\UserMiniinfoAsset;
use app\components\widgets\JdenticonWidget;
use yii\bootstrap\Html;

UserMiniinfoAsset::register($this);

$_icon = AppLinkAsset::register($this);
?>
<div id="user-miniinfo" itemscope itemtype="http://schema.org/Person" itemprop="author">
  <div id="user-miniinfo-box">
    <h2>
      <?= Html::a(
        implode('', [
          Html::tag(
            'span',
            $user->userIcon
              ? Html::img(
                $user->userIcon->url,
                ['width' => '48', 'height' => '48']
              )
              : JdenticonWidget::widget([
                'hash' => $user->identiconHash,
                'class' => 'identicon',
                'size' => '48',
                'schema' => 'image',
              ]),
            ['class' => 'miniinfo-user-icon']
          ),
          Html::tag(
            'span',
            Html::encode($user->name),
            ['class' => 'miniinfo-user-name', 'itemprop' => 'name']
          ),
        ]),
        ['/show-user/profile', 'screen_name' => $user->screen_name]
      ) . "\n" ?>
    </h2>
    <div class="row">
      <div class="col-xs-4">
        <div class="user-label">
          <?= Html::encode(Yii::t('app', 'Battles')) . "\n" ?>
        </div>
        <div class="user-number">
          <?= Html::a(
            Html::encode(Yii::$app->formatter->asInteger($user->getBattle2s()->count())),
            ['show-v2/user', 'screen_name' => $user->screen_name]
          ) . "\n" ?>
        </div>
      </div>
    </div>
    <div class="miniinfo-databox">
<?php if ($user->twitter != ''): ?>
      <div>
        <span class="fa fa-twitter fa-fw"></span>
        <?= Html::a(
          Html::encode('@' . $user->twitter),
          sprintf('https://twitter.com/%s', rawurlencode($user->twitter)),
          ['rel' => 'nofollow', 'target' => '_blank']
        ) . "\n" ?>
      </div>
<?php endif; ?>
<?php if ($user->nnid != ''): ?>
      <div>
        <span class="fa fa-fw"><?=
          $_icon->nnid;
        ?></span>
        <?= Html::a(
          Html::encode($user->nnid),
          sprintf('https://miiverse.nintendo.net/users/%s', rawurlencode($user->nnid)),
          ['rel' => 'nofollow', 'target' => '_blank']
        ) . "\n" ?>
      </div>
<?php endif; ?>
<?php if ($user->sw_friend_code != ''): ?>
      <div>
        <span class="fa fa-fw"><?=
          $_icon->switch
        ?></span>
        <?= Html::tag(
          'span',
          Html::encode(sprintf(
            'SW-%s-%s-%s',
            substr($user->sw_friend_code, 0, 4),
            substr($user->sw_friend_code, 4, 4),
            substr($user->sw_friend_code, 8, 4)
          )),
          ['style' => ['white-space' => 'nowrap']]
        ) . "\n" ?>
      </div>
<?php endif; ?>
<?php if ($user->ikanakama != ''): ?>
      <div>
        <span class="fa fa-fw"><?=
          $_icon->ikanakama
        ?></span>
        <?= Html::a(
          Html::encode(Yii::t('app', 'Ika-Nakama')),
          sprintf('http://ikazok.net/users/%d', $user->ikanakama),
          ['rel' => 'nofollow', 'target' => '_blank']
        ) . "\n" ?>
      </div>
<?php endif; ?>
<?php if ($user->ikanakama2 != ''): ?>
      <div>
        <span class="fa fa-fw"><?=
          $_icon->ikanakama
        ?></span>
        <?= Html::a(
          Html::encode(Yii::t('app', 'Ika-Nakama 2')),
          sprintf('https://ikanakama.ink/users/%d', $user->ikanakama2),
          ['rel' => 'nofollow', 'target' => '_blank']
        ) . "\n" ?>
      </div>
<?php endif; ?>
    </div>
  </div>
</div>
