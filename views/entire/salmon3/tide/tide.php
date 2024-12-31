<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\EntireSalmon3TideAsset;
use app\models\BigrunMap3;
use app\models\SalmonMap3;
use app\models\SalmonWaterLevel2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, BigrunMap3> $bigMaps
 * @var array<int, SalmonMap3> $maps
 * @var array<int, SalmonWaterLevel2> $tides
 * @var array[] $mapTides
 */

EntireSalmon3TideAsset::register($this);

?>
<h2><?= Html::encode(Yii::t('app-salmon-tide2', 'Water Level')) ?></h2>
<div class="row">
<?php foreach ($mapTides as $row) { ?>
<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-3">
<?= $this->render('tide/heading', [
  'bigMap' => $row['big_stage_id'] ? $bigMaps[$row['big_stage_id']] : null,
  'map' => $row['stage_id'] ? $maps[$row['stage_id']] : null,
]) . "\n" ?>
<?= $this->render('tide/pie', [
  'tides' => $tides,
  'values' => $row['tides'],
]) . "\n" ?>
<?= $this->render('tide/n', ['n' => $row['total']]) . "\n" ?>
<?= $this->render('tide/clear', [
  'tides' => $tides,
  'info' => $row,
]) . "\n" ?>
</div>
<?php } ?>
</div>
