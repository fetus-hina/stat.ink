<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon3UserStatsGoldenEgg;
use app\models\Salmon3UserStatsGoldenEggIndividualHistogram;
use app\models\Salmon3UserStatsGoldenEggTeamHistogram;
use app\models\SalmonMap3;
use app\models\User;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3UserStatsGoldenEggIndividualHistogram[] $goldenEggIndividualHistogramData
 * @var Salmon3UserStatsGoldenEggTeamHistogram[] $goldenEggTeamHistogramData
 * @var User $user
 * @var View $this
 * @var array<int, Salmon3UserStatsGoldenEgg> $goldenEggHistogramAbstracts
 * @var array<int, SalmonMap3> $maps
 */

if (!$maps || !$goldenEggHistogramAbstracts) {
  return;
}

?>
<?= Html::tag(
  'h3',
  implode(' ', [
    Icon::goldenEgg(),
    Html::encode(Yii::t('app-salmon2', 'Golden Eggs')),
  ]),
  ['class' => 'mb-3'],
) . "\n" ?>
<?= Tabs::widget([
  'items' => array_map(
    fn (Salmon3UserStatsGoldenEgg $abstract): array => [
      'label' => Yii::t('app-map3', $maps[$abstract->map_id]?->short_name ?? '?'),
      'content' => Html::tag(
        'div',
        implode('', [
          $this->render('golden-eggs/histogram', [
            'title' => Icon::goldenEgg() . ' ' . Html::encode(Yii::t('app-salmon3', 'Team Total')),
            'abstract' => $abstract,
            'data' => array_values(
              array_filter(
                $goldenEggTeamHistogramData,
                fn (Salmon3UserStatsGoldenEggTeamHistogram $model): bool => $model->map_id === $abstract->map_id,
              ),
            ),
          ]),
          $this->render('golden-eggs/histogram', [
            'title' => Icon::goldenEgg() . ' ' . Html::encode(Yii::t('app-salmon3', 'Personal')),
            'abstract' => $abstract,
            'data' => array_values(
              array_filter(
                $goldenEggIndividualHistogramData,
                fn (Salmon3UserStatsGoldenEggIndividualHistogram $model): bool => $model->map_id === $abstract->map_id,
              ),
            ),
          ]),
        ]),
        ['class' => 'row'],
      ),
    ],
    $goldenEggHistogramAbstracts,
  ),
  'options' => ['class' => 'mt-0 mb-3'],
  'tabContentOptions' => ['class' => 'mt-3'],
]) . "\n" ?>
