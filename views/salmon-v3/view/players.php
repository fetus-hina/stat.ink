<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\v3\SalmonPlayers;
use app\models\Salmon3;
use app\models\SalmonPlayer3;
use app\models\SalmonWave3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var SalmonWave3[] $waves
 * @var View $this
 */

$players = SalmonPlayer3::find()
  ->with([
    'salmonPlayerWeapon3s',
    'salmonPlayerWeapon3s.weapon',
    'special',
    'species',
    'uniform',
  ])
  ->andWhere(['salmon_id' => $model->id])
  ->orderBy([
    'is_me' => SORT_DESC,
    'golden_eggs' => SORT_DESC,
    'power_eggs' => SORT_DESC,
    'golden_assist' => SORT_DESC,
    'rescue' => SORT_DESC,
    'rescued' => SORT_ASC,
    'defeat_boss' => SORT_DESC,
    'name' => SORT_ASC,
    'id' => SORT_ASC,
  ])
  ->all();
if (!$players) {
  return;
}

?>
<?= Html::tag('h2', Html::encode(Yii::t('app', 'Players'))) . "\n" ?>
<?= SalmonPlayers::widget([
  'job' => $model,
  'players' => array_values($players),
  'waves' => $waves,
]) . "\n" ?>
