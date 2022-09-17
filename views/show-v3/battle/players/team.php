<?php

declare(strict_types=1);

use app\models\BattlePlayer3;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var bool $ourTeam
 * @var BattlePlayer3[] $players
 */

$f = Yii::$app->formatter;

$total = function (string $attrName) use ($players): ?int {
  return array_reduce(
    array_map(
      function (BattlePlayer3 $model) use ($attrName): ?int {
        $v = filter_var($model->{$attrName} ?? null, FILTER_VALIDATE_INT);
        return is_int($v) ? $v : null;
      },
      $players
    ),
    fn (?int $carry, ?int $item): ?int => ($carry !== null && $item !== null)
      ? $carry + $item
      : null,
    0
  );
};

$totalK = $total('kill');
$totalD = $total('death');

?>
<tr class="bg-warning">
  <th colspan="3"><?= Html::encode(Yii::t('app', $ourTeam ? 'Good Guys' : 'Bad Guys')) ?></th>
  <td class="text-right"><?= $f->asInteger($total('inked')) ?></td>
  <td class="text-right"><?= $f->asInteger($totalK) ?></td>
  <td class="text-right"><?= $f->asInteger($totalD) ?></td>
  <td class="text-right"><?php
    if ($totalK !== null && $totalD !== null) {
      if ($totalD > 0) {
        echo $f->asDecimal($totalK / $totalD, 2);
      } elseif ($totalK > 0) {
        echo $f->asDecimal(99.99, 2);
      } else {
        echo '-';
      }
    }
  ?></td>
  <td class="text-right"><?= $f->asInteger($total('special')) ?></td>
</tr>
<?php
foreach (array_values($players) as $i => $player) {
  echo $this->render('//show-v3/battle/players/player', [
    'isFirst' => $i === 0,
    'nPlayers' => count($players),
    'player' => $player,
  ]) . "\n";
}
