<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\models\Rule3;
use app\models\Special3;
use app\models\StatSpecialUse3;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3|null $rule
 * @var array<int, Special3> $specials
 * @var StatSpecialUse3[] $data
 */

TableResponsiveForceAsset::register($this);

$fmt = Yii::$app->formatter;

?>
<div class="mb-3">
<?php if ($rule) { ?>
  <?= Html::tag(
    'h2',
    vsprintf('%s %s', [
      Html::img(
        Yii::$app->assetManager->getAssetUrl(
          Yii::$app->assetManager->getBundle(GameModeIconsAsset::class),
          sprintf('spl3/%s.png', $rule->key),
        ),
        [
          'class' => 'basic-icon',
          'style' => ['--icon-height' => '1em'],
        ],
      ),
      Html::encode(Yii::t('app-rule3', $rule->name)),
    ]),
    [
      'class' => 'm-0 mb-3',
      'id' => $rule->key,
    ],
  ) . "\n" ?>

<?php } ?>
  <div class="table-responsive table-responsive-force">
    <?= GridView::widget([
      'dataProvider' => Yii::createObject([
        'class' => ArrayDataProvider::class,
        'allModels' => $data,
        'key' => 'special_id',
        'pagination' => false,
        'sort' => false,
      ]),
      'emptyCell' => '',
      'layout' => '{items}',
      'columns' => [
        [
          'label' => Yii::t('app', 'Special'),
          'format' => 'raw',
          'value' => fn (StatSpecialUse3 $model): string => vsprintf('%s %s', [
            SpecialIcon::widget(['model' => $specials[$model->special_id] ?? null]),
            Html::encode(Yii::t('app-special3', $specials[$model->special_id]?->name ?? '')),
          ]),
        ],
        [
          'label' => Yii::t('app', 'Avg. Uses'),
          'contentOptions' => ['class' => 'text-right'],
          'value' => fn (StatSpecialUse3 $model): string => $model->stddev === null
            ? $fmt->asDecimal($model->avg_uses, 2)
            : vsprintf('%s (σ=%s)', [
              $fmt->asDecimal($model->avg_uses, 2),
              $fmt->asDecimal($model->stddev, 2),
            ]),
        ],
        [
          'label' => Yii::t('app', 'Median'),
          'contentOptions' => ['class' => 'text-right'],
          'format' => 'integer',
          'attribute' => 'percentile_50',
        ],
        [
          'label' => Yii::t('app', '{percentile} Percentile', ['percentile' => 25]),
          'contentOptions' => ['class' => 'text-right'],
          'format' => 'integer',
          'attribute' => 'percentile_25',
        ],
        [
          'label' => Yii::t('app', '{percentile} Percentile', ['percentile' => 75]),
          'contentOptions' => ['class' => 'text-right'],
          'format' => 'integer',
          'attribute' => 'percentile_75',
        ],
        [
          'label' => Yii::t('app', 'Win %'),
          'contentOptions' => ['class' => 'text-center'],
          'format' => ['percent', 2],
          'value' => fn (StatSpecialUse3 $model): ?float => $model->sample_size > 0
            ? $model->win / $model->sample_size
            : null,
        ],
        [
          'label' => Yii::t('app', 'Samples'),
          'contentOptions' => ['class' => 'text-right'],
          'format' => 'integer',
          'attribute' => 'sample_size',
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
