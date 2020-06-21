<?php

/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */
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
