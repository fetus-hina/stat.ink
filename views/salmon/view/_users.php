<?php

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\SalmonPlayers;
use app\models\Salmon2;
use yii\web\View;

/**
 * @var Salmon2 $model
 * @var View $this
 */

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
