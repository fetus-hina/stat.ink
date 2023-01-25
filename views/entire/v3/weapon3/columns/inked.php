<?php

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
