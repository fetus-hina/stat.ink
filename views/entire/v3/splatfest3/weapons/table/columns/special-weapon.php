<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Splatfest3StatsWeapon;

return [
  'label' => Yii::t('app', 'Special'),
  'value' => fn (Splatfest3StatsWeapon $model): string => Yii::t('app-special3', $model->special->name),
];
