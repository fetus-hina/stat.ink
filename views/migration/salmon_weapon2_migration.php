<?php

declare(strict_types=1);

use app\components\db\SalmonWeaponMigration;
use app\models\Weapon2;
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
    'traits' => [
        SalmonWeaponMigration::class,
    ],
    'upCode' => implode("\n", [
        '$this->upSalmonWeapons2([',
        "    'key',",
        "    'key',",
        "    'key',",
        ']);',
    ]),
    'downCode' => implode("\n", [
        '$this->downSalmonWeapons2([',
        "    'key',",
        "    'key',",
        "    'key',",
        ']);',
    ]),
]) ?>
