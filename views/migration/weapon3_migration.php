<?php

/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */

/**
 * @var string $className string the new migration class name without namespace
 * @var string $namespace string the new migration class namespace
 */

use app\components\db\Weapon3Migration;
use app\models\Special3;
use app\models\Subweapon3;
use app\models\WeaponType3;
use yii\helpers\ArrayHelper;

$sub = implode(
  ', ',
  ArrayHelper::getColumn(
    Subweapon3::find()->orderBy(['key' => SORT_ASC])->all(),
    'key',
  ),
);

$special = implode(
  ', ',
  ArrayHelper::getColumn(
    Special3::find()->orderBy(['key' => SORT_ASC])->all(),
    'key',
  ),
);

$type = implode(
  ', ',
  ArrayHelper::getColumn(
    WeaponType3::find()->orderBy(['key' => SORT_ASC])->all(),
    'key',
  ),
);

?>
<?= $this->renderFile(__DIR__ . '/migration.php', [
    'className' => $className,
    'namespace' => $namespace,
    'inTransaction' => true,
    'traits' => [
        Weapon3Migration::class,
    ],
    'upCode' => implode(
        "\n",
        [
            "// type: {$type}",
            "// sub: {$sub}",
            "// special: {$special}",
            "\$this->upWeapon3(",
            "    key: 'key',",
            "    name: 'Name',",
            "    type: 'shooter',",
            "    // sub: 'splashbomb',",
            "    // special: 'nicedama',",
            "    // main: 'wakaba',",
            "    // canonical: 'wakaba',",
            "    // salmon: false, // skip-salmon",
            "    // aliases: [],",
            ");",
            "",
            "return true;",
        ],
    ),
    'downCode' => implode(
        "\n",
        [
            "\$this->downWeapon3('key', salmon: true);",
            "",
            "return true;",
        ],
    ),
    'extraCode' => implode(
        "\n",
        [
            "protected function vacuumTables(): array",
            "{",
            "    return [",
            "        '{{%mainweapon3}}',",
            "        '{{%weapon3}}',",
            "        '{{%weapon3_alias}}',",
            "        '{{%salmon_weapon3}}',",
            "        '{{%salmon_weapon3_alias}}',",
            "    ];",
            "}",
        ],
    ),
]) ?>
