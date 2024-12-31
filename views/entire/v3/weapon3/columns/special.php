<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
