<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use app\models\StatWeapon3XUsagePerVersion;
use yii\web\View;

/**
 * @var StatWeapon3Usage[]|StatWeapon3UsagePerVersion[]|StatWeapon3XUsage[]|StatWeapon3XUsagePerVersion[] $data
 * @var View $this
 */

echo $this->render('includes/chart', [
  'data' => $data,
  'getX' => function (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $model): int|float|null {
    return $model->avg_kill - $model->avg_death;
  },
  'xLabel' => Yii::t('app', 'K-D'),
]);
