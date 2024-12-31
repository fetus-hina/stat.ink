<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\grid\CalcKillRatioColumn;

return [
  '-label' => Yii::t('app', 'Kill Rate'),
  'class' => CalcKillRatioColumn::class,
  'headerOptions' => ['class' => 'cell-kill-rate'],
  'killRate' => true,
];
