<?php

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
