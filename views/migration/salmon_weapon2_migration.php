<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

use app\models\Weapon2;
use yii\web\View;

/**
 * @var View $this
 * @var string $className the new migration class name without namespace
 * @var string $namespace the new migration class namespace
 */
?>
<?= $this->renderFile(__DIR__ . '/migration.php', [
    'className' => $className,
    'namespace' => $namespace,
    'inTransaction' => true,
    'traits' => [
        'app\components\db\SalmonWeaponMigration',
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
