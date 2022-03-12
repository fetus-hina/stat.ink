<?php

declare(strict_types=1);

use app\components\db\GearMigration;
use app\models\Ability2;
use app\models\Brand2;
use yii\base\View;

/**
 * @var View $this
 * @var string $className
 * @var string $namespace
 */

$ability = implode(', ', array_map(
    function (Ability2 $model): string {
        return $model->key;
    },
    Ability2::find()->orderBy(['key' => SORT_ASC])->all()
));

$brand = implode(', ', array_map(
    function (Brand2 $model): string {
        return $model->key;
    },
    Brand2::find()->orderBy(['key' => SORT_ASC])->all()
));
?>
<?= $this->renderFile(__DIR__ . '/migration.php', [
    'className' => $className,
    'namespace' => $namespace,
    'inTransaction' => true,
    'traits' => [
        GearMigration::class,
    ],
    'upCode' => implode("\n", [
        "foreach (\$this->getGears() as \$gearData) {",
        "    call_user_func_array([\$this, 'upGear2'], \$gearData);",
        "}",
    ]),
    'downCode' => implode("\n", [
        "foreach (\$this->getGears() as \$gearData) {",
        "    \$this->downGear2(\$gearData[0]);",
        "}",
    ]),
    'extraCode' => implode("\n", [
        "public function getGears(): array",
        "{",
        "    // types: headgear, clothing, shoes",
        "    // brands: {$brand}",
        "    // abilities: {$ability}",
        "    return [",
        "        [",
        "            static::name2key('Name'),",
        "            'Name',",
        "            'type',",
        "            'brand',",
        "            'ability',",
        "            null, // splatnet ID",
        "        ],",
        "    ];",
        "}",
    ]),
]) ?>
