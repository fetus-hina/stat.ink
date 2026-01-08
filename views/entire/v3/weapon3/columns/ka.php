<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\StatWeapon3KillOrAssist;
use app\models\StatWeapon3KillOrAssistPerVersion;
use yii\web\View;

/**
 * @var View $this
 * @var StatWeapon3KillOrAssist[]|StatWeapon3KillOrAssistPerVersion[] $data
 */

echo $this->render('includes/column', [
  'data' => $data,
  'xGet' => 'kill_or_assist',
  'xLabel' => Yii::t('app', 'Kill or Assist'),
]);
