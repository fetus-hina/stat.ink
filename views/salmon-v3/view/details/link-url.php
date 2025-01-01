<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\v3\BattleEditableUrlWidget;
use app\models\Salmon3;

return [
  'contentOptions' => [
    'class' => 'omit',
    'id' => 'link-cell',
  ],
  'format' => 'raw',
  'label' => Yii::t('app', 'URL related to this battle'),
  'value' => function (Salmon3 $model): ?string {
    $html = BattleEditableUrlWidget::widget(['model' => $model]);
    return $html ?: null;
  },
];
