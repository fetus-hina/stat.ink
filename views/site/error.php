<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $message
 */

?>
<div class="container">
  <h1>
    Error
  </h1>
  <p>
    <?= Html::encode($message) . "\n" ?>
  </p>
</div>
