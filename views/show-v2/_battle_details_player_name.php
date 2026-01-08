<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\IrasutoyaAsset;
use app\components\widgets\FA;
use app\models\User;
use statink\yii2\anonymizer\AnonymizerAsset;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 */

$namePartInner = trim(implode(' ', [
  // identicon {{{
  (function () use ($player): string {
    if (!$url = $player->iconUrl) {
      return '';
    }
    return Html::img(
      $url,
      [
        'class' => 'auto-tooltip',
        'title' => (trim((string)$player->splatnet_id) !== '')
          ? sprintf('ID: %s', $player->splatnet_id)
          : '',
        'style' => [
          'width' => '1.2em',
          'height' => 'auto',
        ],
      ]
    );
  })(),
  // }}}
  // top player {{{
  (function () use ($player): string {
    if (!$player->top_500) {
      return '';
    }

    return Html::tag('span', '', [
      'class' => 'fas fa-fw fa-chess-queen',
    ]);
  })(),
  // }}}
  // name {{{
  (function () use ($battle, $player, $teamKey): string {
    $anonymize = false;
    if ($player->is_me) {
      // "me" always shown
      $anonymize = false;
    } elseif ($player->isForceBlackouted) {
      $anonymize = true;
    } elseif (trim((string)$player->name) === '') {
      // We can only show an anonymized name
      $anonymize = true;
    } else {
      $user = Yii::$app->user;
      if (!$user->isGuest && $user->identity->id == $battle->user_id) {
        // Logged in user is also battle owner.
        // All users' name will be shown
        $anonymize = false;
      } else {
        // respect user's setting
        $blackoutMode = $battle->user->blackout_list ?? 'always';
        switch ($blackoutMode) {
          case User::BLACKOUT_NOT_BLACKOUT:
            $anonymize = false;
            break;

          case User::BLACKOUT_NOT_PRIVATE:
            if (($battle->lobby->key ?? '') === 'private' || ($battle->mode->key ?? '') === 'private') {
              $anonymize = false;
            } else {
              $anonymize = true;
            }
            break;

          case User::BLACKOUT_NOT_FRIEND:
            if (($battle->lobby->key ?? '') === 'private' || ($battle->mode->key ?? '') === 'private') {
              $anonymize = false;
            } elseif ($battle->isGachi && ($battle->lobby->key ?? '') === 'squad_4' && ($teamKey === 'my')) {
              $anonymize = false;
            } else {
              $anonymize = true;
            }
            break;

          case User::BLACKOUT_ALWAYS:
          default:
            $anonymize = true;
            break;
        }
      }
    }
    if (!$anonymize && trim((string)$player->name) !== '') {
      return Html::encode(trim((string)$player->name));
    } else {
      AnonymizerAsset::register($this);
      return Html::tag(
        'span',
        Html::encode(str_repeat('*', 10)),
        [
          'title' => Yii::t('app', 'Anonymized'),
          'class' => 'auto-tooltip anonymize',
          'data' => [
            'anonymize' => (function (string $raw) : string {
              return substr(
                hash(
                  'sha256',
                  (preg_match('/^([0-9a-f]{2}+)[0-9a-f]?$/', $raw, $match))
                    ? hex2bin($match[1])
                    : $raw
                ),
                0,
                40
              );
            })($player->anonymizeSeed),
          ],
        ]
      );
    }
  })(),
  // }}}
]));

$user = $player->getUser();
$namePart = $user
  ? Html::a($namePartInner, ['show-user/profile', 'screen_name' => $user->screen_name])
  : Html::tag('span', $namePartInner);

$speciesIconInner = '';
if ($player->species) {
  $asset = IrasutoyaAsset::register($this);
  $img = $asset->img($player->species->key . '.png', [
    'alt' => Yii::t('app', $player->species->name),
    'title' => Yii::t('app', $player->species->name),
    'class' => 'auto-tooltip',
    'style' => [
      'height' => 'calc(1.2em - 2px)',
    ],
  ]);
  $speciesIconInner = Html::tag('span', $img, [
    'style' => [
      'display' => 'inline-block',
      'line-height' => '1',
      'padding' => '1px',
      'background' => $player->species->key === 'inkling' ? '#333' : '#ddd',
      'border-radius' => '4px',
    ],
  ]);
}

$playerBattleLink = [];
if (
  $historyCount > 1 &&
  !$player->is_me &&
  $player->splatnet_id !== null &&
  preg_match('/^[0-9a-f]{16}$/', (string)$player->splatnet_id)
) {
  $playerBattleLink[] = Html::a(
    (string)FA::fas('history'),
    ['show-v2/user',
      'screen_name' => $battle->user->screen_name,
      'filter' => [
        'filter' => sprintf('with:%s', (string)$player->splatnet_id),
      ],
    ],
    [
      'class' => 'mr-1 auto-tooltip',
      'title' => Yii::t('app', '{nFormatted} {n, plural, =1{battle} other{battles}}', [
        'n' => $historyCount,
        'nFormatted' => Yii::$app->formatter->asInteger($historyCount),
      ]),
    ]
  );
}

echo Html::tag(
  'div',
  implode('', [
    Html::tag('span', $namePart),
    Html::tag('span', implode('', [
      implode('', $playerBattleLink),
      $speciesIconInner,
    ])),
  ]),
  [
    'style' => [
      'display' => 'flex',
      'align-items' => 'center',
      'justify-content' => 'space-between',
    ],
  ]
);
