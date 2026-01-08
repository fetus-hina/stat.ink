<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;

?>
<div class="alert alert-warning mb-3 py-0">
  <p class="my-3">
    <?= implode(' ', [
      Icon::splatoon1(),
      Icon::splatoon2(),
      Icon::splatoon3(),
    ]) . ':' . "\n" ?>
    <?= Html::encode(
      Yii::t('app', 'Items marked with these icons will only work with its corresponding version.'),
    ) . "\n" ?>
  </p>
</div>
