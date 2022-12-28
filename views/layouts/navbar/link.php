<?php

declare(strict_types=1);

use app\assets\AppLinkAsset;
use app\components\widgets\FlagIcon;
use app\components\widgets\GameVersionIcon;
use app\components\widgets\Icon;
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
    // S3 official {{{
    'name' => implode(' ', [
      GameVersionIcon::widget(['version' => 3]),
      Html::encode(Yii::t('app', '{title} Official Website', [
        'title' => Yii::t('app', 'Splatoon 3'),
      ])),
    ]),
    'sub' => [
      [
        'url' => 'https://www.nintendo.co.jp/switch/av5ja/',
        'name' => implode(' ', [
          $jp(),
          Html::encode(Yii::t('app', 'Japan')),
        ]),
      ],
      [
        'url' => 'https://splatoon.nintendo.com/',
        'name' => implode(' ', [
          $us(),
          Html::encode(Yii::t('app', 'North America')),
        ]),
      ],
      [
        'url' => 'https://www.nintendo.co.uk/Games/Nintendo-Switch-games/Splatoon-3-1924751.html',
        'name' => implode(' ', [
          $eu(),
          Html::encode(Yii::t('app', 'Europe')),
        ]),
      ],
    ],
    // }}}
  ],
  [
    // S2 official {{{
    'name' => implode(' ',[
      GameVersionIcon::widget(['version' => 2]),
      Html::encode(Yii::t('app', '{title} Official Website', [
        'title' => Yii::t('app', 'Splatoon 2'),
      ])),
    ]),
    'sub' => [
      [
        'url' => 'https://www.nintendo.co.jp/switch/aab6a/',
        'name' => implode(' ', [
          $jp(),
          Html::encode(Yii::t('app', 'Japan')),
        ]),
      ],
      [
        'url' => 'https://splatoon.nintendo.com/',
        'name' => implode(' ', [
          $us(),
          Html::tag('del', Html::encode(Yii::t('app', 'North America'))),
        ]),
      ],
      [
        'url' => 'https://www.nintendo.co.uk/Games/Nintendo-Switch-games/Splatoon-2-1173295.html',
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
    'name' => implode(' ',[
      GameVersionIcon::widget(['version' => 1]),
      Html::encode(Yii::t('app', '{title} Official Website', [
        'title' => Yii::t('app', 'Splatoon'),
      ])),
    ]),
    'sub' => [
      [
        'url' => 'https://www.nintendo.co.jp/wiiu/agmj/',
        'name' => implode(' ', [
          $jp(),
          Html::encode(Yii::t('app', 'Japan')),
        ]),
      ],
      [
        'url' => 'http://splatoon.nintendo.com/splatoon/',
        'name' => implode(' ', [
          $us(),
          Html::tag('del', Html::encode(Yii::t('app', 'North America'))),
        ]),
      ],
      [
        'url' => 'https://www.nintendo.co.uk/Games/Wii-U-games/Splatoon-892510.html',
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
    'name' => implode(' ', [
      Icon::twitter(),
      Html::encode(Yii::t('app', 'Official Twitter')),
    ]),
    'sub' => [
      [
        'url' => 'https://twitter.com/SplatoonJP',
        'name' => implode(' ', [
          $jp(),
          Html::encode(Yii::t('app', 'Japan')),
        ]),
      ],
      [
        'url' => 'https://twitter.com/SplatoonNA',
        'name' => implode(' ', [
          $us(),
          Html::encode(Yii::t('app', 'North America')),
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
    // Splatoon Base website {{{
    'name' => Html::encode(Yii::t('app', 'Splatoon Base Official Website')),
    'sub' => [
      [
        'url' => 'https://www.nintendo.co.jp/character/splatoon/',
        'name' => implode(' ', [
          $jp(),
          Html::encode(Yii::t('app', 'Japanese')),
        ]),
      ],
      [
        'url' => 'https://www.nintendo.co.jp/character/splatoon/en/',
        'name' => implode(' ', [
          $us(),
          Html::encode(Yii::t('app', 'English')),
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
          Icon::android(),
          Html::encode(Yii::t('app', 'Android')),
        ]),
      ],
      [
        'url' => 'https://apps.apple.com/app/nintendo-switch-online/id1234806557',
        'name' => implode('', [
          Icon::ios(),
          Html::encode(Yii::t('app', 'iOS (iPhone/iPad)')),
        ]),
      ],
    ],
    // }}}
  ],
  [],
  [
    // s3s
    'url' => 'https://github.com/frozenpandaman/s3s',
    'name' => implode(' ', [
      Html::encode(Yii::t('app', 's3s')),
      Icon::windows(),
      Icon::macOs(),
      Icon::linux(),
    ]),
  ],
  [
    // s3si.ts
    'url' => 'https://github.com/spacemeowx2/s3si.ts',
    'name' => implode(' ', [
      Html::encode(Yii::t('app', 's3si.ts')),
      Icon::windows(),
      Icon::macOs(),
      Icon::linux(),
    ]),
  ],
  [],
  [
    'name' => implode(' ', [
      GameVersionIcon::widget(['version' => 2]),
      Html::encode(Yii::t('app', 'Apps for {version}', ['version' => Yii::t('app', 'Splatoon 2')])),
    ]),
    'sub' => [
      [
        'url' => 'https://github.com/frozenpandaman/splatnet2statink',
        'name' => implode(' ', [
          Html::encode(Yii::t('app', 'splatnet2statink')),
          Icon::windows(),
          Icon::macOs(),
          Icon::linux(),
        ]),
      ],
      [
        'url' => 'https://github.com/hymm/squid-tracks',
        'name' => implode(' ', [
          $icon->squidTracks,
          Html::tag('del', Html::encode(Yii::t('app', 'SquidTracks'))),
          Icon::windows(),
          Icon::macOs(),
          Icon::linux(),
        ]),
      ],
      [
        'name' => implode(' ', [
          $icon->ikarecJa,
          Html::tag('del', Html::encode(Yii::t('app', 'IkaRec 2'))),
          Icon::android(),
        ]),
        'url' => 'https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec2',
      ],
    ],
  ],
  [
    'name' => implode(' ', [
      GameVersionIcon::widget(['version' => 1]),
      Html::encode(Yii::t('app', 'Apps for {version}', ['version' => Yii::t('app', 'Splatoon 1')])),
    ]),
    'sub' => [
      [
        'name' => implode(' ', [
          $icon->ikalog,
          Html::encode(Yii::t('app', 'IkaLog')),
          Icon::windows(),
          Icon::macOs(),
          Icon::linux(),
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
            'name' => implode(' ', [
              Icon::download(),
              Html::encode(Yii::t('app', 'IkaLog Download Page')),
              '(' . Icon::Windows() . ' ' . Html::encode(Yii::t('app', 'Windows')) . ')',
            ]),
          ],
        ],
      ],
      [
        'url' => 'https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec',
        'name' => implode(' ', [
          $icon->ikarecJa,
          $jp(),
          Html::tag(
            'del',
            implode(' ', [
              Html::encode(Yii::t('app', 'IkaRec')),
              '(' . Html::encode(Yii::t('app', 'for {title}', [
                'title' => Yii::t('app', 'Splatoon'),
              ])) . ' / 日本語)',
            ]),
          ),
          Icon::android(),
        ]),
      ],
      [
        'url' => 'https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec',
        'name' => implode(' ', [
          $icon->ikarecEn,
          $us(),
          Html::encode(Yii::t('app', 'IkaRec')),
          '(' . Html::encode(Yii::t('app', 'for {title}', [
            'title' => Yii::t('app', 'Splatoon'),
          ])) . ' / English)',
          Icon::android(),
        ]),
      ],
    ],
  ],
  [],
  [
    'url' => 'https://ikanakama.ink/',
    'name' => implode(' ', [
      $icon->ikanakama,
      Html::encode(Yii::t('app', 'Ika-Nakama')),
    ]),
  ],
  [
    'url' => 'https://fest.ink/',
    'name' => implode(' ', [
      $icon->festink,
      Html::encode(Yii::t('app', 'fest.ink')),
    ]),
  ],
  [],
  [
    'url' => 'https://blog.fetus.jp/',
    'name' => implode(' ', [
      Icon::blog(),
      Html::encode(Yii::t('app', 'Blog')),
    ]),
  ],
  [
    'url' => 'https://github.com/fetus-hina/stat.ink',
    'name' => implode(' ', [
      Icon::github(),
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
    Icon::link(),
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
