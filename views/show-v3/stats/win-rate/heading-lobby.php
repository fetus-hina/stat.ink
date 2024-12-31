<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
