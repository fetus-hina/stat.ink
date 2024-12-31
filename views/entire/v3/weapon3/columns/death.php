<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\StatWeapon3Death;
use app\models\StatWeapon3DeathPerVersion;
use yii\web\View;

/**
 * @var View $this
 * @var StatWeapon3Death[]|StatWeapon3DeathPerVersion[] $data
 */

echo $this->render('includes/column', [
  'data' => $data,
  'xGet' => 'death',
  'xLabel' => Yii::t('app', 'Deaths'),
]);
