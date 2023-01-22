<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use yii\web\View;

/**
 * @var StatWeapon3Usage[]|StatWeapon3UsagePerVersion[] $data
 * @var View $this
 */

echo $this->render('includes/chart', [
  'data' => $data,
  'getX' => function (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): int|float|null {
    return $model->avg_death > 0
      ? $model->avg_kill / $model->avg_death
      : null;
  },
  'xLabel' => Yii::t('app', 'Kill Ratio'),
]);
