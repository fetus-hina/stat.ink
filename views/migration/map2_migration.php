<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\base\View;

/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */

/**
 * @var View $this
 * @var string $className string the new migration class name without namespace
 * @var string $namespace string the new migration class namespace
 */

?>
<?= $this->renderFile(__DIR__ . '/migration.php', [
    'className' => $className,
    'namespace' => $namespace,
    'inTransaction' => true,
    'traits' => [],
    'upCode' => implode("\n", [
        "\$this->insert('map2', [",
        "    'key' => '',",
        "    'name' => '',",
        "    'short_name' => '',",
        "    'area' => null,",
        "    'splatnet' => null,",
        "    'release_at' => 'YYYY-MM-01T00:00:00+00:00',",
        "]);",
    ]),
    'downCode' => implode("\n", [
        "\$this->delete('map2', [",
        "    'key' => '',",
        "]);",
    ]),
]) ?>
