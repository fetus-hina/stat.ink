<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Knockout3;
use app\models\Knockout3Histogram;
use app\models\Map3;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Map3 $map
 * @var View $this
 * @var array<int, Knockout3> $data
 * @var array<int, Rule3> $rules
 */

?>
<tr>
  <?= Html::tag(
    'th',
    Html::encode(Yii::t('app-map3', $map->name)),
    [
      'class' => 'text-center align-middle pb-3',
      'scope' => 'row',
    ],
  ) . "\n" ?>
<?php foreach ($rules as $ruleId => $rule) { ?>
  <?= Html::tag(
    'td',
    implode("\n", [
      $this->render('cell-pie', [
        'model' => $data[$ruleId] ?? null,
      ]),
      $this->render('cell-histogram', [
        'data' => Knockout3Histogram::find()
          ->andWhere([
            'season_id' => $season->id,
            'rule_id' => $rule->id,
            'map_id' => $map->id,
          ])
          ->orderBy(['class_value' => SORT_ASC])
          ->all(),
      ]),
    ]),
    ['class' => 'pb-3'],
  ) . "\n" ?>
<?php } ?>
</tr>
