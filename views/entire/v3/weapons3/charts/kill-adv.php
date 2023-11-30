<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use yii\web\View;

/**
 * @var StatWeapon3Usage[]|StatWeapon3UsagePerVersion[]|StatWeapon3XUsage[] $data
 * @var View $this
 */

echo $this->render('includes/chart', [
  'data' => $data,
  'getX' => function (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage $model): int|float|null {
    return $model->avg_kill - $model->avg_death;
  },
  'xLabel' => Yii::t('app', 'K-D'),
]);
