<?php

declare(strict_types=1);

use app\assets\LineSeedJpAsset;
use app\assets\LineSeedJpThAsset;
use app\components\widgets\UserIcon;
use app\models\BattlePlayer3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var User $user
 */

LineSeedJpAsset::register($this);
LineSeedJpThAsset::register($this);

if (Yii::$app->request->isPjax) {
  echo '<div id="person-box" class="col-xs-12 col-md-3"></div>';
  return;
}

$latestPlayer3 = BattlePlayer3::find()
  ->innerJoinWith(['battle'], false)
  ->with(['splashtagTitle'])
  ->andWhere(['and',
    [
      '{{%battle3}}.[[is_deleted]]' => false,
      '{{%battle3}}.[[user_id]]' => $user->id,
      '{{%battle_player3}}.[[is_me]]' => true,
    ],
    ['not', ['{{%battle_player3}}.[[name]]' => null]],
    ['not', ['{{%battle_player3}}.[[number]]' => null]],
    ['<>', '{{%battle_player3}}.[[name]]', ''],
    ['<>', '{{%battle_player3}}.[[number]]', ''],
  ])
  ->orderBy([
    '{{%battle3}}.[[end_at]]' => SORT_DESC,
    '{{%battle3}}.[[id]]' => SORT_DESC,
  ])
  ->limit(1)
  ->one();

$css = [
  '#person-box h1' => [
    'font-family' => 'LINE Seed JP,sans-serif',
    'font-size' => '30px',
    'font-weight' => 'bold',
    'margin' => '15px 0 5px',
    'padding' => '0',
  ],
  '#person-box h2' => [
    'font-family' => 'LINE Seed JP,sans-serif',
    'font-size' => '24px',
    'font-weight' => '100',
    'margin' => '5px 0',
    'padding' => '0',
  ],
  '#person-box .splashtag' => [
    'font-family' => 'LINE Seed JP,sans-serif',
    'font-size' => '16px',
    'font-weight' => '500',
    'margin' => '5px 0',
    'padding' => '0',
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
<?php if ($latestPlayer3) { ?>
  <?= Html::tag(
    'p',
    implode(
      Html::tag('br'),
      array_map(
        fn (string $text): string => Html::encode($text),
        array_filter(
          [
            $latestPlayer3->splashtagTitle
              ? $latestPlayer3->splashtagTitle->name
              : null,
            vsprintf('%s #%s', [
              $latestPlayer3->name,
              $latestPlayer3->number,
            ]),
          ],
          fn (?string $text): ?bool => $text !== null,
        ),
      ),
    ),
    [
      'class' => 'splashtag',
    ],
  ) . "\n" ?>
<?php } ?>
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
