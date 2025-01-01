<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\StatWeapon3Kill;
use app\models\StatWeapon3KillPerVersion;
use yii\web\View;

/**
 * @var View $this
 * @var StatWeapon3Kill[]|StatWeapon3KillPerVersion[] $data
 */

echo $this->render('includes/column', [
  'data' => $data,
  'xGet' => 'kill',
  'xLabel' => Yii::t('app', 'Kills'),
]);
