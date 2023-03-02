<?php

declare(strict_types=1);

use app\models\StatXPowerDistrib3;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JqueryAsset;
use yii\web\View;

/**
 * @var StatXPowerDistrib3[] $data
 * @var View $this
 */

if (!$data) {
  return;
}

?>
<div class="row">
  <div class="col-xs-12 col-md-9 col-lg-7 mb-3">
    <?= Html::tag('div', '', [
      'class' => 'ratio ratio-16x9 xpower-distrib-chart',
      'data' => [
        'translates' => Json::encode([
            'Users' => Yii::t('app', 'Users'),
        ]),
        'dataset' => Json::encode(
          array_map(
            fn (StatXPowerDistrib3 $v): array => [
              'x' => (int)$v->x_power,
              'y' => (int)$v->users,
            ],
            $data,
          ),
        ),
      ],
    ]) . "\n" ?>
  </div>
</div>
