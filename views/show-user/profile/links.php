<?php

declare(strict_types=1);

use app\assets\AppLinkAsset;
use app\components\widgets\Icon;
use app\models\User;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var User $user
 */

$asset = AppLinkAsset::register($this);

$css = [
  '#person-box ul, #person-box li' => [
    'display' => 'block',
    'list-style-type' => 'none',
    'margin' => 0,
    'padding' => 0,
  ],
  '#profile .tab-content' => [
    'margin-top' => '15px',
  ],
];

/** @var string[] */
$links = [];
if ($user->twitter) {
  $links[] = implode('', [
    Icon::twitter(),
    Html::a(
      Html::encode('@' . $user->twitter),
      sprintf('https://twitter.com/%s', rawurlencode($user->twitter)),
      [
        'rel' => 'nofollow noopener',
        'target' => '_blank',
      ]
    ),
  ]);
}

if ($user->nnid) {
  $links[] = implode('', [
    Html::tag('span', $asset->nnid, ['class' => 'fa fa-fw']),
    Html::encode($user->nnid),
  ]);
}

if ($user->sw_friend_code) {
  $links[] = implode('', [
    Html::tag('span', $asset->switch, ['class' => 'fa fa-fw']),
    Html::encode(implode('-', [
      'SW',
      substr($user->sw_friend_code, 0, 4),
      substr($user->sw_friend_code, 4, 4),
      substr($user->sw_friend_code, 8, 4),
    ])),
  ]);
}

if ($user->ikanakama2) {
  $links[] = implode('', [
    Html::tag('span', $asset->ikanakama, ['class' => 'fa fa-fw']),
    Html::a(
      Yii::t('app', 'Ika-Nakama'),
      sprintf('https://ikanakama.ink/users/%d', $user->ikanakama2),
      [
        'rel' => 'nofollow noopener',
        'target' => '_blank',
      ]
    ),
  ]);
}

if ($links) {
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

  echo Html::tag('ul', implode('', array_map(
    fn (string $html): string => Html::tag('li', $html),
    $links
  )));
}
