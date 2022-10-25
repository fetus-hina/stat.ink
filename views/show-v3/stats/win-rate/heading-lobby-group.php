<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\LobbyGroup3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var LobbyGroup3 $lobbyGroup
 * @var View $this
 */

$assetManager = Yii::$app->assetManager;

$imgClass = sprintf('img-%s', substr(hash('sha256', __FILE__), 0, 8));
$this->registerCss(vsprintf('.%s{%s}', [
  $imgClass,
  Html::cssStyleFromArray([
    'height' => '1em',
    'vertical-align' => 'middle',
    'width' => 'auto',
  ]),
]));

?>
<?= Html::tag(
  'h2',
  implode('', array_filter(
    [
      $assetManager
        ? Html::img(
          $assetManager->getAssetUrl(
            $assetManager->getBundle(GameModeIconsAsset::class),
            sprintf('spl3/%s.png', $lobbyGroup->key),
          ),
          ['class' => $imgClass],
        )
        : null,
      Html::encode(Yii::t('app-lobby3', $lobbyGroup->name)),
    ],
    fn (?string $text): bool => $text !== null,
  )),
  [
    'class' => 'mt-2 mb-2',
    'id' => sprintf('lobby-%s', $lobbyGroup->key),
  ],
) ?>
