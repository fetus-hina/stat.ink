<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $samples
 */

?>
<div class="mb-3">
  <p class="mb-1">
    <?= Html::encode(
      Yii::t('app', 'Aggregated: {rules}', [
        'rules' => Yii::t('app', '7 players for each battle (excluded the battle uploader)'),
      ]),
    ) . "\n" ?>
  </p>
  <p class="mb-1">
    <?= Html::encode(
      vsprintf('%s: %s', [
        Yii::t('app', 'Samples'),
        Yii::$app->formatter->asInteger($samples),
      ]),
    ) . "\n" ?>
  </p>
</div>
