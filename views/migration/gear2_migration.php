<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Ability2;
use app\models\Brand2;
use yii\base\View;

/**
 * @var View $this
 * @var string $className the new migration class name without namespace
 * @var string $namespace the new migration class namespace
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
        'app\components\db\GearMigration',
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
