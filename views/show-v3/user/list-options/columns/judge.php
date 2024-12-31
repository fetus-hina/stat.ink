<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;
use yii\web\View;

/**
 * @var View $this
 */

return [
  'contentOptions' => ['class' => 'cell-judge'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-judge'],
  'label' => Yii::t('app', 'Judge'),
  'value' => fn (Battle3 $model): string => $this->render(
    '//show-v3/user/battle_judge',
    ['model' => $model],
  ),
];
