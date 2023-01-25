<?php

declare(strict_types=1);

use app\models\StatWeapon3Special;
use app\models\StatWeapon3SpecialPerVersion;
use yii\web\View;

/**
 * @var View $this
 * @var StatWeapon3Special[]|StatWeapon3SpecialPerVersion[] $data
 */

echo $this->render('includes/column', [
  'data' => $data,
  'xGet' => 'special',
  'xLabel' => Yii::t('app', 'Specials'),
]);
