<?php

declare(strict_types=1);

use app\models\Special3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Special3 $special
 * @var View $this
 * @var array<int, array{battles: int, wins: int}> $data
 */

$fmt = Yii::$app->formatter;
$maxUses = max(array_keys($data));
$maxSamples = max(ArrayHelper::getColumn($data, 'battles'));

?>
<?= Html::beginTag(
  'table',
  [
    'class' => [
      'table',
      'table-bordered',
      'table-striped',
    ],
  ],
) . "\n" ?>
  <thead>
    <tr>
      <th class="text-center"><?= Html::encode(Yii::t('app', 'Times')) ?></th>
      <th class="text-center"><?= Html::encode(Yii::t('app', 'Win %')) ?></th>
      <th colspan="2" class="text-center"><?= Html::encode(Yii::t('app', 'Samples')) ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach (range(0, $maxUses) as $uses) { ?>
    <?= Html::tag(
      'tr',
      implode('', [
        Html::tag(
          'th',
          Html::encode($fmt->asInteger($uses)),
          ['class' => 'text-center', 'scope' => 'row'],
        ),
        $this->render('table/col-winpct', ['data' => $data[$uses] ?? null]),
        $this->render('table/col-samples', [
          'data' => $data[$uses] ?? null,
          'maxSamples' => $maxSamples,
        ]),
      ]),
    ) . "\n" ?>
<?php } ?>
  </tbody>
</table>
