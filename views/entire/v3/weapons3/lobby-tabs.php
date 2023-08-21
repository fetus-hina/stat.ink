<?php

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

echo Html::tag(
  'nav',
  Html::tag(
    'ul',
    implode('', [
      $this->render('lobby-tabs/lobbies', compact('lobby', 'lobbies', 'lobbyUrl')),
      Html::tag(
        'li',
        Html::a(
          implode(' ', [
            Icon::s3LobbyEvent(),
            Html::tag(
              'span',
              Html::encode(Yii::t('app-lobby3', 'Challenge')),
              ['class' => 'd-none d-sm-inline'],
            ),
          ]),
          ['entire/event3'],
        ),
        ['role' => 'presentation'],
      ),
    ]),
    ['class' => 'nav nav-pills'],
  ),
  ['class' => 'mb-1'],
);
