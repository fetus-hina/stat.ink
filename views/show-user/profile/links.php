<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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

if ($user->sw_friend_code) {
  $links[] = implode('', [
    Html::encode(implode('-', [
      'SW',
      substr($user->sw_friend_code, 0, 4),
      substr($user->sw_friend_code, 4, 4),
      substr($user->sw_friend_code, 8, 4),
    ])),
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
