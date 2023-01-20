<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Lobby3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var View $this
 * @var array<int, Lobby3> $lobbies
 * @var callable(Lobby3): string $lobbyUrl
 */

$icon = fn (Lobby3 $lobby): string => Html::img(
  Yii::$app->assetManager->getAssetUrl(
    GameModeIconsAsset::register($this),
    sprintf('spl3/%s.png', $lobby->key),
  ),
  [
    'class' => 'basic-icon',
    'draggable' => 'false',
    'style' => [
      '--icon-height' => '1em',
    ],
  ],
);

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
            implode(' ', [
              $icon($item),
              Html::tag(
                'span',
                Html::encode(Yii::t('app-lobby3', $item->name)),
                $item->key === $lobby->key
                  ? []
                  : ['class' => 'd-none d-md-inline'],
              ),
            ]),
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
