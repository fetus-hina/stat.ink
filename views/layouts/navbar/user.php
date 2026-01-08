<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\components\widgets\UserIcon;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$user = Yii::$app->user->identity ?? null;

?>
<?= Html::a(
  implode(' ', [
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
      : Icon::user(),
    $user
      ? Html::encode($user->name)
      : Html::encode(Yii::t('app', 'Guest')),
    ' ',
    Html::tag('span', '', ['class' => 'caret']),
  ]),
  '#',
  [
    'aria-expanded' => 'false',
    'aria-haspopup' => 'true',
    'class' => 'dropdown-toggle',
    'data' => ['toggle' => 'dropdown'],
    'role' => 'button',
  ]
) . "\n" ?>
<?= Html::tag('ul', implode('', array_merge(
  $user
    ? [
      Html::tag('li', Html::a(
        implode(' ', [
          Icon::user(),
          Html::encode(Yii::t('app', 'Your Battles')),
        ]),
        ['/show-user/profile', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '┣', ['class' => 'fa fa-fw']),
          Html::encode(Yii::t('app', 'Splatoon 3')),
        ]),
        ['/show-v3/user', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '┃', ['class' => 'fa fa-fw']),
          Html::tag('span', '┗', ['class' => 'fa fa-fw']),
          Html::encode(Yii::t('app-salmon2', 'Salmon Run')),
        ]),
        ['salmon-v3/index', 'screen_name' => $user->screen_name],
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
          Html::tag('span', '┃', ['class' => 'fa fa-fw']),
          Html::tag('span', '┗', ['class' => 'fa fa-fw']),
          Html::encode(Yii::t('app-salmon2', 'Salmon Run')),
        ]),
        ['salmon/index', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode('', [
          Html::tag('span', '┗', ['class' => 'fa fa-fw']),
          Html::encode(Yii::t('app', 'Splatoon')),
        ]),
        ['/show/user', 'screen_name' => $user->screen_name]
      )),
      Html::tag('li', Html::a(
        implode(' ', [
          Icon::config(),
          Html::encode(Yii::t('app', 'Profile and Settings')),
        ]),
        ['/user/profile']
      )),
      Html::tag('li', Html::a(
        implode('', [
          Icon::loginHistory(),
          Html::encode(Yii::t('app', 'Login History')),
        ]),
        ['/user/login-history']
      )),
      Html::tag('li', '', ['class' => 'divider']),
      Html::tag('li', Html::a(
        implode(' ', [
          Icon::logout(),
          Html::encode(Yii::t('app', 'Logout')),
        ]),
        ['/user/logout']
      )),
    ]
    : [
      Html::tag('li', Html::a(
        implode(' ', [
          Icon::login(),
          Html::encode(Yii::t('app', 'Login')),
        ]),
        ['/user/login']
      )),
      (Yii::$app->params['twitter']['read_enabled'] ?? false)
        ? Html::tag('li', Html::a(
          implode('', [
            Html::tag('span', '┗', ['class' => 'fa fa-fw']),
            Icon::twitter(),
            ' ',
            Html::encode(Yii::t('app', 'Log in with Twitter')),
          ]),
          ['/user/login-with-twitter']
        ))
        : '',
      Html::tag('li', Html::a(
        implode(' ', [
          Icon::userAdd(),
          Html::encode(Yii::t('app', 'Register')),
        ]),
        ['/user/register']
      )),
    ],
  [
    Html::tag('li', '', ['class' => 'divider']),
    Html::tag('li', implode('', [
      Html::a(
        implode(' ', [
          Icon::colorScheme(),
          Html::encode(Yii::t('app', 'Color Scheme')),
        ]),
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
