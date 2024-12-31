<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\StatWeapon3Inked;
use app\models\StatWeapon3InkedPerVersion;
use yii\web\View;

/**
 * @var View $this
 * @var StatWeapon3Inked[]|StatWeapon3InkedPerVersion[] $data
 */

echo $this->render('includes/column', [
  'data' => $data,
  'xGet' => 'inked',
  'xLabel' => Yii::t('app', 'Turf Inked'),
]);
