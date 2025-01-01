<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Lobby3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var View $this
 * @var array<int, Lobby3> $lobbies
 * @var callable(Lobby3): string $lobbyUrl
 */

echo implode(
  '',
  array_map(
    fn (Lobby3 $item): string => Html::tag(
      'li',
      Html::tag(
        'a',
        trim(
          implode(' ', [
            Icon::s3Lobby($item),
            Html::tag(
              'span',
              Html::encode(Yii::t('app-lobby3', $item->name)),
              ['class' => 'd-none d-sm-inline'],
            ),
          ]),
        ),
        $item->key !== $lobby->key
          ? ['href' => $lobbyUrl($item)]
          : [],
      ),
      [
        'role' => 'presentation',
        'class' => $item->key === $lobby->key ? 'active': null,
      ],
    ),
    $lobbies,
  ),
);
