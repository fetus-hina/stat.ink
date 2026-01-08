<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\EntireKnockoutAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Knockout3;
use app\models\Knockout3Histogram;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Rule3;
use app\models\Season3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Knockout3[] $data
 * @var Knockout3[] $total
 * @var Lobby3 $xMatch
 * @var Season3 $season
 * @var Season3[] $seasons
 * @var View $this
 * @var array<int, Map3> $maps
 * @var array<int, Rule3> $rules
 * @var callable(Season3): string $seasonUrl
 */

/**
 * @var array<int, Knockout3> $mappedTotal
 */
$mappedTotal = ArrayHelper::map(
  $total,
  'rule_id',
  fn (Knockout3 $v): Knockout3 => $v,
);

?>
<tr>
  <?= Html::tag('th', $this->render('../../../knockout/legends')) . "\n" ?>
<?php foreach ($rules as $id => $rule) { ?>
  <?= Html::tag(
    'td',
    implode("\n", [
      $this->render('cell-pie', [
        'model' => $mappedTotal[$id] ?? null,
      ]),
      $this->render('cell-histogram', [
        'data' => Knockout3Histogram::find()
          ->andWhere([
            'season_id' => $season->id,
            'rule_id' => $rule->id,
            'map_id' => null,
          ])
          ->orderBy(['class_value' => SORT_ASC])
          ->all(),
      ]),
    ]),
    ['class' => 'pb-3'],
  ) . "\n" ?>
<?php } ?>
</tr>
