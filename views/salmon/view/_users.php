<?php
declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\SalmonPlayers;

$players = $model->players;
if (!$players) {
  return;
}
?>
<h2><?= Yii::t('app', 'Players') ?></h2>
<?= SalmonPlayers::widget([
  'work' => $model,
  'players' => $players,
  'formatter' => Yii::createObject([
    'class' => Formatter::class,
    'nullDisplay' => '',
  ]),
]) ?>
