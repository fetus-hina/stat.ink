<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\StatWeapon3Assist;
use app\models\StatWeapon3AssistPerVersion;
use yii\web\View;

/**
 * @var View $this
 * @var StatWeapon3Assist[]|StatWeapon3AssistPerVersion[] $data
 */

echo $this->render('includes/column', [
  'data' => $data,
  'xGet' => 'assist',
  'xLabel' => Yii::t('app', 'Assists'),
]);
