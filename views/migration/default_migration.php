<?php

declare(strict_types=1);

use yii\base\View;

/**
 * @var View $this
 * @var string $className
 * @var string $namespace
 */

?>
<?= $this->renderFile(__DIR__ . '/migration.php', [
    'className' => $className,
    'namespace' => $namespace,
    'inTransaction' => true,
    'upCode' => '',
    'downCode' => implode("\n", [
        "echo \"" . addslashes($className) . " cannot be reverted.\\n\";",
        "return false;",
    ]),
]) ?>
