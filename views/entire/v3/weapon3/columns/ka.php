<?php

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
