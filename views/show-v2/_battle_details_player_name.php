<?php
use app\assets\IrasutoyaAsset;
use app\models\User;
use statink\yii2\anonymizer\AnonymizerAsset;
use yii\bootstrap\Html;

$namePartInner = trim(implode(' ', [
  // identicon {{{
  (function () use ($player) : string {
    if (!$url = $player->iconUrl) {
      return '';
    }
    return Html::img(
      $url,
      [
        'class' => 'auto-tooltip',
        'title' => (trim($player->splatnet_id) !== '')
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
  (function () use ($player) : string {
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
    } elseif (trim($player->name) === '') {
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
    if (!$anonymize && trim($player->name) !== '') {
      return Html::encode(trim($player->name));
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

echo Html::tag(
    'div',
    $namePart . '<span>' . $speciesIconInner . '</span>',
    [
        'style' => [
            'display' => 'flex',
            'align-items' => 'center',
            'justify-content' => 'space-between',
        ],
    ]
);
