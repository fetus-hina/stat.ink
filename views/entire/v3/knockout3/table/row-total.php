<?php

declare(strict_types=1);

use app\assets\EntireKnockoutAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Knockout3;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\Map3;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;

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
  <?= $this->render('cell-pie', [
    'model' => $mappedTotal[$id] ?? null,
  ]) . "\n" ?>
<?php } ?>
</tr>
