<?php
use yii\helpers\Html;

$user = Yii::$app->user->identity ?? null;
$placeholder = function (string $theme): string {
    return Html::tag('span', '', ['class' => [
      'fas',
      'fa-fw',
      $theme === Yii::$app->theme->theme ? 'fa-check' : '',
    ]]) . ' ';
};

$this->registerCss('.fa-twitter{color:#1da1f2}');

$themes = [
    'bootswatch-cosmo' => 'Cosmo',
    'bootswatch-cyborg' => 'Cyborg',
    'bootswatch-darkly' => 'Darkly',
    'bootswatch-flatly' => 'Flatly',
    'bootswatch-paper' => 'Paper',
    'bootswatch-slate' => 'Slate',
];
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
      Html::a(Yii::t('app', 'Color Scheme'), 'javascript:;', ['data-toggle' => 'dropdown']),
      Html::tag('ul', implode('', [
        Html::tag('li', Html::a(
          $placeholder('default') . Yii::t('app', 'Default Color'),
          'javascript:;',
          ['class' => 'theme-switcher', 'data-theme' => 'default']
        )),
        Html::tag('li', Html::a(
          $placeholder('color-blind') . Yii::t('app', 'Color-Blind Support'),
          'javascript:;',
          ['class' => 'theme-switcher', 'data-theme' => 'color-blind']
        )),
        Html::tag('li', '', ['class' => 'divider']),
        implode('', array_map(
          function (string $themeId, string $themeName) use ($placeholder): string {
            return Html::tag('li', Html::a(
              $placeholder($themeId) . Yii::t('app', '{theme} Theme', ['theme' => $themeName]),
              'javascript:;',
              ['class' => 'theme-switcher', 'data-theme' => $themeId]
            ));
          },
          array_keys($themes),
          array_values($themes)
        )),
      ]), ['class' => 'dropdown-menu']),
    ]), ['class' => 'dropdown-submenu']),
    Html::tag('li', Html::a(
      implode('', [
        Html::tag('span', '', ['class' => 'far fa-fw']),
        Html::encode(Yii::t('app', 'Use full width of the screen')),
      ]),
      'javascript:;',
      ['id' => 'toggle-use-fluid']
    )),
  ]
)), ['class' => 'dropdown-menu']) ?>
