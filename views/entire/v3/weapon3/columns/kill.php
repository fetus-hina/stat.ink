<?php

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
