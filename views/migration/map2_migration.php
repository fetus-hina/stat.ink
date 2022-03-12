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
