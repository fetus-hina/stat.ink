<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;

return [
  'contentOptions' => [
    'class' => 'omit',
    'id' => 'replay-cell',
  ],
  'label' => Yii::t('app', 'Replay Code'),
  'format' => 'replayCode3',
  'attribute' => 'replay_code',
];
