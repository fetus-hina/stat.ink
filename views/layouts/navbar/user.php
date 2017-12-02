<?php
use yii\helpers\Html;

$user = Yii::$app->user->identity ?? null;

$this->registerCss('.fa-twitter{color:#1da1f2}');
?>
<?= Html::a(
  implode('', [
    $user
      ? Html::img($user->iconUrl, [
        'class' => 'fa fa-fw',
        'style' => [
          'width' => '1em',
          'height' => '1em',
          'background-color' => '#fff',
        ],
      ])
      : Html::tag('span', '', ['class' => 'fa fa-fw fa-user']),
    $user
      ? Html::encode($user->name)
      : Html::encode(Yii::t('app', 'Guest')),
    ' ',
    Html::tag('span', '', ['class' => 'caret']),
  ]),
  'javascript:;',
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
          Html::encode(Yii::t('app', 'Splatoon 2')),
        ]),
        ['/show-v2/user', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '┗', ['class' => 'fa fa-fw']),
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
      Html::tag('li', '', ['class' => 'divider']),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fa fa-fw fa-sign-out']),
          Html::encode(Yii::t('app', 'Logout')),
        ]),
        ['/user/logout']
      )),
    ]
    : [
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '', ['class' => 'fa fa-fw fa-sign-in']),
          Html::encode(Yii::t('app', 'Login')),
        ]),
        ['/user/login']
      )),
      (Yii::$app->params['twitter']['read_enabled'] ?? false)
        ? Html::tag('li', Html::a(
          implode('', [
            Html::tag('span', '┗', ['class' => 'fa fa-fw']),
            Html::tag('span', '', ['class' => 'fa fa-fw fa-twitter']),
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
    Html::tag('li', Html::a(
      implode('', [
        Html::tag('span', '', ['class' => 'fa fa-fw']),
        Html::encode(Yii::t('app', 'Color-Blind Support')),
      ]),
      'javascript:;',
      ['id' => 'toggle-color-lock']
    )),
    Html::tag('li', Html::a(
      implode('', [
        Html::tag('span', '', ['class' => 'fa fa-fw']),
        Html::encode(Yii::t('app', 'Use full width of the screen')),
      ]),
      'javascript:;',
      ['id' => 'toggle-use-fluid']
    )),
  ]
)), ['class' => 'dropdown-menu']) ?>
