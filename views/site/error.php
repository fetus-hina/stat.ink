<?php

declare(strict_types=1);

use app\components\helpers\Html;
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
