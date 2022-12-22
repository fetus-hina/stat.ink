<?php

declare(strict_types=1);

use app\models\Knockout3;
use app\models\Map3;
use app\models\Rule3;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var Knockout3[] $data
 * @var Knockout3[] $total
 * @var View $this
 * @var array<int, Map3> $maps
 * @var array<int, Rule3> $rules
 */

?>
<tbody>
  <?= $this->render('row-total', compact('total', 'rules')) . "\n" ?>
<?php foreach ($maps as $mapId => $map) { ?>
  <?= $this->render('row-map', [
    'map' => $map,
    'rules' => $rules,
    'data' => ArrayHelper::map(
      array_filter(
        $data,
        fn (Knockout3 $model): bool => $model->map_id === $mapId,
      ),
      'rule_id',
      fn (Knockout3 $v): Knockout3 => $v,
    ),
  ]) . "\n" ?>
<?php } ?>
</tbody>
