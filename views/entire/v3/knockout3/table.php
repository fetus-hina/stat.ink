<?php

declare(strict_types=1);

use app\models\Knockout3;
use app\models\Map3;
use app\models\Rule3;
use app\models\Season3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Knockout3[] $data
 * @var Knockout3[] $total
 * @var Season3 $season
 * @var View $this
 * @var array<int, Map3> $maps
 * @var array<int, Rule3> $rules
 */

$this->registerCss(vsprintf('.graph-container{%s}', [
  Html::cssStyleFromArray([
    'min-width' => sprintf('%dpx', 220 * (count($rules) + 1)),
  ]),
]));

?>
<div class="table-responsive table-responsive-force">
  <table class="table table-condensed graph-container">
    <?= $this->render('table/header', compact('rules')) . "\n" ?>
    <?= $this->render('table/body', compact('data', 'maps', 'rules', 'season', 'total')) . "\n" ?>
  </table>
</div>
