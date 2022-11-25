<?php

declare(strict_types=1);

use app\components\widgets\FA;
use app\components\widgets\GameVersionIcon;
use app\components\widgets\UserIcon;
use yii\helpers\Html;

$user = Yii::$app->user->identity ?? null;

?>
<?= Html::a(
  implode('', [
    $user
      ? UserIcon::widget([
        'user' => $user,
        'options' => [
          'style' => [
            'width' => 'auto',
            'height' => '1em',
            'background-color' => '#fff',
          ],
        ],
      ])
      : Html::tag('span', '', ['class' => 'fa fa-fw fa-user']),
    $user
      ? Html::encode($user->name)
      : Html::encode(Yii::t('app', 'Guest')),
    ' ',
    Html::tag('span', '', ['class' => 'caret']),
  ]),
  '#',
  [
    'class' => 'dropdown-toggle',
    'data' => [
      'toggle' => 'dropdown',
    ],
    'role' => 'button',
    'aria-haspopup' => 'true',
    'aria-expanded' => 'false',
  ]
) . "\n" ?>
<?= Html::tag('ul', implode('', array_merge(
  $user
    ? [
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fa fa-fw fa-user']),
          Html::encode(Yii::t('app', 'Your Battles')),
        ]),
        ['/show-user/profile', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '┣', ['class' => 'fa fa-fw']),
          GameVersionIcon::widget(['version' => 3]),
          Html::encode(Yii::t('app', 'Splatoon 3')),
        ]),
        ['/show-v3/user', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '┃', ['class' => 'fa fa-fw']),
          Html::tag('span', '┗', ['class' => 'fa fa-fw']),
          Html::tag('span', '', ['class' => 'fas fa-fw fa-fish']),
          Html::encode(Yii::t('app-salmon2', 'Salmon Run')),
        ]),
        ['salmon-v3/index', 'screen_name' => $user->screen_name],
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '┣', ['class' => 'fa fa-fw']),
          GameVersionIcon::widget(['version' => 2]),
          Html::encode(Yii::t('app', 'Splatoon 2')),
        ]),
        ['/show-v2/user', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '┃', ['class' => 'fa fa-fw']),
          Html::tag('span', '┗', ['class' => 'fa fa-fw']),
          Html::tag('span', '', ['class' => 'fas fa-fw fa-fish']),
          Html::encode(Yii::t('app-salmon2', 'Salmon Run')),
        ]),
        ['salmon/index', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '┗', ['class' => 'fa fa-fw']),
          GameVersionIcon::widget(['version' => 1]),
          Html::encode(Yii::t('app', 'Splatoon')),
        ]),
        ['/show/user', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fa fa-fw fa-wrench']),
          Html::encode(Yii::t('app', 'Profile and Settings')),
        ]),
        ['/user/profile']
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fa fa-fw fa-user-clock']),
          Html::encode(Yii::t('app', 'Login History')),
        ]),
        ['/user/login-history']
      )),
      Html::tag('li', '', ['class' => 'divider']),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fa fa-fw fa-sign-out-alt']),
          Html::encode(Yii::t('app', 'Logout')),
        ]),
        ['/user/logout']
      )),
    ]
    : [
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fa fa-fw fa-sign-in-alt']),
          Html::encode(Yii::t('app', 'Login')),
        ]),
        ['/user/login']
      )),
      (Yii::$app->params['twitter']['read_enabled'] ?? false)
        ? Html::tag('li', Html::a(
          implode('', [
            Html::tag('span', '┗', ['class' => 'fa fa-fw']),
            Html::tag('span', '', ['class' => 'fab fa-fw fa-twitter']),
            Html::encode(Yii::t('app', 'Log in with Twitter')),
          ]),
          ['/user/login-with-twitter']
        ))
        : '',
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fa fa-fw fa-plus']),
          Html::encode(Yii::t('app', 'Register')),
        ]),
        ['/user/register']
      )),
    ],
  [
    Html::tag('li', '', ['class' => 'divider']),
    Html::tag('li', implode('', [
      Html::a(
        FA::fas('palette')->fw() . ' ' . Yii::t('app', 'Color Scheme'),
        '#color-scheme-dialog',
        [
          'data-toggle' => 'modal',
          'aria-haspopup' => 'true',
        ]
      ),
    ]), ['class' => 'dropdown-submenu']),
    Html::tag('li', Html::a(
      implode('', [
        Html::tag('span', '', ['class' => 'far fa-fw']),
        Html::encode(Yii::t('app', 'Use full width of the screen')),
      ]),
      '#',
      ['id' => 'toggle-use-fluid']
    )),
  ]
)), ['class' => 'dropdown-menu']) ?>
