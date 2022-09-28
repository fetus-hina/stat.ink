<?php

declare(strict_types=1);

use app\assets\AppLinkAsset;
use app\components\widgets\FA;
use app\components\widgets\FlagIcon;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$icon = AppLinkAsset::register($this);
$jp = function (): string {
  return (string)FlagIcon::fg('jp');
};
$eu = function (): string {
  return (string)FlagIcon::fg('eu');
};
$us = function (): string {
  return (String)FlagIcon::fg('us');
};

$list = [
  [
    // S2 official {{{
    'name' => Html::encode(Yii::t('app', '{title} Official Website', [
      'title' => Yii::t('app', 'Splatoon 2'),
    ])),
    'sub' => [
      [
        'url' => 'https://www.nintendo.co.jp/switch/aab6a',
        'name' => implode(' ', [
          $jp(),
          Html::encode(Yii::t('app', 'Japan')),
        ]),
      ],
      [
        'url' => 'http://splatoon.nintendo.com/',
        'name' => implode(' ', [
          $us(),
          Html::encode(Yii::t('app', 'North America')),
        ]),
      ],
      [
        'url' => 'https://www.nintendo.co.uk/Games/Nintendo-Switch/Splatoon-2-1173295.html',
        'name' => implode(' ', [
          $eu(),
          Html::encode(Yii::t('app', 'Europe')),
        ]),
      ],
    ],
    // }}}
  ],
  [
    // S1 official {{{
    'name' => Html::encode(Yii::t('app', '{title} Official Website', [
      'title' => Yii::t('app', 'Splatoon'),
    ])),
    'sub' => [
      [
        'url' => 'http://www.nintendo.co.jp/wiiu/agmj/',
        'name' => implode(' ', [
          $jp(),
          Html::encode(Yii::t('app', 'Japan')),
        ]),
      ],
      [
        'url' => 'http://splatoon.nintendo.com/splatoon/',
        'name' => implode(' ', [
          $us(),
          Html::encode(Yii::t('app', 'North America')),
        ]),
      ],
      [
        'url' => 'https://www.nintendo.co.uk/Games/Wii-U/Splatoon-892510.html',
        'name' => implode(' ', [
          $eu(),
          Html::encode(Yii::t('app', 'Europe')),
        ]),
      ],
    ],
    // }}}
  ],
  [
    // Twitter {{{
    'name' => implode('', [
      (string)FA::fab('twitter')->fw(),
      Html::encode(Yii::t('app', 'Official Twitter')),
    ]),
    'sub' => [
      [
        'url' => 'https://twitter.com/splatoonjp',
        'name' => implode(' ', [
          $jp(),
          Html::encode(Yii::t('app', 'Japan')),
        ]),
      ],
      [
        'url' => 'https://twitter.com/NintendoAmerica',
        'name' => implode(' ', [
          $us(),
          Html::encode(Yii::t('app', 'North America')),
          Html::encode('(Nintendo)'),
        ]),
      ],
      [
        'url' => 'https://twitter.com/NintendoVS',
        'name' => implode(' ', [
          $us(),
          Html::encode(Yii::t('app', 'North America')),
          Html::encode('(Nintendo VS)'),
        ]),
      ],
      [
        'url' => 'https://twitter.com/NintendoEurope',
        'name' => implode(' ', [
          $eu(),
          Html::encode(Yii::t('app', 'Europe')),
          Html::encode('(Nintendo)'),
        ]),
      ],
    ],
    // }}}
  ],
  [
    // Official app {{{
    'name' => Html::encode(Yii::t('app', 'Nintendo Switch Online app')),
    'sub' => [
      [
        'url' => 'https://play.google.com/store/apps/details?id=com.nintendo.znca',
        'name' => implode('', [
          (string)FA::fab('android')->fw(),
          Html::encode(Yii::t('app', 'Android')),
        ]),
      ],
      [
        'url' => 'https://itunes.apple.com/app/nintendo-switch-online/id1234806557',
        'name' => implode('', [
          (string)FA::fab('apple')->fw(),
          Html::encode(Yii::t('app', 'iOS (iPhone/iPad)')),
        ]),
      ],
    ],
    // }}}
  ],
  [],
  [
    // splatnet2statink {{{
    'url' => 'https://github.com/frozenpandaman/s3s/',
    'name' => implode('', [
      Html::tag('span', '', ['class' => 'fa fa-fw']),
      Html::encode(Yii::t('app', 's3s')),
      (string)FA::fab('windows')->fw(),
      (string)FA::fab('apple')->fw(),
      (string)FA::fab('linux')->fw(),
    ]),
    // }}}
  ],
  [],
  [
    // SquidTracks {{{
    'url' => 'https://github.com/hymm/squid-tracks/',
    'name' => implode('', [
      $icon->squidTracks,
      Html::encode(Yii::t('app', 'SquidTracks')),
      (string)FA::fab('windows')->fw(),
      (string)FA::fab('apple')->fw(),
      (string)FA::fab('linux')->fw(),
    ]),
    // }}}
  ],
  [
    // splatnet2statink {{{
    'url' => 'https://github.com/frozenpandaman/splatnet2statink/',
    'name' => implode('', [
      Html::tag('span', '', ['class' => 'fa fa-fw']),
      Html::encode(Yii::t('app', 'splatnet2statink')),
      (string)FA::fab('windows')->fw(),
      (string)FA::fab('apple')->fw(),
      (string)FA::fab('linux')->fw(),
    ]),
    // }}}
  ],
  [
    // IkaRec 2 {{{
    'name' => implode('', [
      $icon->ikarecJa,
      Html::encode(Yii::t('app', 'IkaRec 2')),
      Html::tag('span', '', ['class' => 'fab fa-fw fa-android']),
    ]),
    'url' => 'https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec2',
    // }}}
  ],
  [],
  [
    'name' => implode('', [
      Html::tag('span', '', ['class' => 'fa fa-fw']),
      Html::encode(Yii::t('app', 'Apps for {version}', ['version' => Yii::t('app', 'Splatoon 1')])),
    ]),
    'sub' => [
      [
        // IkaLog {{{
        'name' => implode('', [
          $icon->ikalog,
          Html::encode(Yii::t('app', 'IkaLog')),
          (string)FA::fab('windows')->fw(),
          (string)FA::fab('apple')->fw(),
          (string)FA::fab('linux')->fw(),
        ]),
        'sub' => [
          [
            'url' => 'https://github.com/hasegaw/IkaLog/wiki/ja_WinIkaLog',
            'name' => implode(' ', [
              $jp(),
              Html::encode('日本語'),
            ]),
          ],
          [
            'url' => 'https://github.com/hasegaw/IkaLog/wiki/en_Home',
            'name' => implode(' ', [
              $us(),
              Html::encode('English'),
            ]),
          ],
          [],
          [
            'url' => 'https://hasegaw.github.io/IkaLog/',
            'name' => implode('', [
              (string)FA::fas('download')->fw(),
              Html::encode(Yii::t('app', 'IkaLog Download Page')),
              '(' . FA::fab('windows')->fw() . Html::encode(Yii::t('app', 'Windows')) . ')',
            ]),
          ],
        ],
        // }}}
      ],
      [
        // IkaRec {{{
        'url' => 'https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec',
        'name' => implode('', [
          $icon->ikarecJa,
          $jp(),
          Html::encode(Yii::t('app', 'IkaRec')),
          '(' . Html::encode(Yii::t('app', 'for {title}', [
            'title' => Yii::t('app', 'Splatoon'),
          ])) . ' / 日本語)',
          (string)FA::fab('android')->fw(),
        ]),
      ],
      [
        'url' => 'https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec',
        'name' => implode('', [
          $icon->ikarecEn,
          $us(),
          Html::encode(Yii::t('app', 'IkaRec')),
          '(' . Html::encode(Yii::t('app', 'for {title}', [
            'title' => Yii::t('app', 'Splatoon'),
          ])) . ' / English)',
          (string)FA::fab('android')->fw(),
        ]),
        // }}}
      ],
    ],
  ],
  [],
  [
    'url' => 'https://ikadenwa.ink/',
    'name' => implode('', [
      $icon->ikadenwa,
      Html::encode(Yii::t('app', 'Ika-Denwa')),
    ]),
  ],
  [
    'url' => 'https://ikanakama.ink/',
    'name' => implode('', [
      $icon->ikanakama,
      Html::encode(Yii::t('app', 'Ika-Nakama 2')),
    ]),
  ],
  [
    'url' => 'https://fest.ink/',
    'name' => implode('', [
      $icon->festink,
      Html::encode(Yii::t('app', 'fest.ink')),
    ]),
  ],
  [],
  [
    'url' => 'https://blog.fetus.jp/',
    'name' => implode('', [
      (string)FA::fab('wordpress')->fw(),
      Html::encode(Yii::t('app', 'Blog')),
    ]),
  ],
  [
    'url' => 'https://github.com/fetus-hina/stat.ink',
    'name' => implode('', [
      (string)FA::fab('github-alt')->fw(),
      Html::encode(Yii::t('app', 'Source Code')),
    ]),
  ],
];

$renderEntry = function (array $entry) use (&$renderEntry) : string {
  return $entry
    ? Html::tag(
      'li',
      implode('', [
        Html::a(
          $entry['name'],
          $entry['url'] ?? '#',
          ($entry['sub'] ?? null) ? ['data-toggle' => 'dropdown'] : []
        ),
        ($entry['sub'] ?? null)
          ? Html::tag(
            'ul',
            implode('', array_map($renderEntry, $entry['sub'])),
            ['class' => 'dropdown-menu']
          )
          : ''
      ]),
      ['class' => ($entry['sub'] ?? null) ? 'dropdown-submenu' : '']
    )
    : Html::tag('li', '', ['class' => 'divider']);
}
?>
<?= Html::a(
  implode('', [
    (string)FA::fas('link')->fw(),
    Html::encode(Yii::t('app', 'Links')),
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
<?= Html::tag('ul', implode('', array_map($renderEntry, $list)), ['class' => 'dropdown-menu']) ?>
