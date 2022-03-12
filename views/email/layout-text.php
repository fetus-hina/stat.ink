<?php

declare(strict_types=1);

use yii\helpers\Url;

/**
 * @var string $content
 */

?>
<?= trim((string)$content) . "\n" ?>

-- 
<?= Yii::$app->name . "\n" ?>
<?= Url::home(true) . "\n" ?>
