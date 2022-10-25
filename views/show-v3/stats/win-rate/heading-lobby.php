<?php

declare(strict_types=1);

use app\models\Lobby3;
use app\models\LobbyGroup3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var LobbyGroup3 $lobbyGroup
 * @var Lobby3 $lobby
 * @var View $this
 */

?>
<?= Html::tag(
  'h3',
  Html::encode(Yii::t('app-lobby3', $lobby->name)),
  [
    'class' => 'mt-3 mb-3',
    'id' => sprintf('lobby-%s-%s', $lobbyGroup->key, $lobby->key),
  ],
) ?>
