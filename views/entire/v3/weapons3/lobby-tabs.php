<?php

declare(strict_types=1);

use app\models\Lobby3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var View $this
 * @var array<int, Lobby3> $lobbies
 * @var callable(Lobby3): string $lobbyUrl
 */

?>
<nav class="mb-1">
  <?= Html::tag(
    'ul',
    implode(
      '',
      array_map(
        fn (Lobby3 $item): string => Html::tag(
          'li',
          Html::tag(
            'a',
            Html::encode(Yii::t('app-lobby3', $item->name)),
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
    ),
    ['class' => 'nav nav-pills'],
  ) . "\n" ?>
</nav>
