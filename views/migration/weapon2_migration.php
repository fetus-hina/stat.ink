<?php

use app\models\Special2;
use app\models\Subweapon2;
use app\models\WeaponType2;
use yii\base\View;

/**
 * @var string $className string the new migration class name without namespace
 * @var string $namespace string the new migration class namespace
 * @var View $this
 */

$sortUnique = function (array $values): array {
    sort($values);
    return array_values(array_unique($values));
};

$sub = implode(', ', array_map(
    function (Subweapon2 $model): string {
        return $model->key;
    },
    Subweapon2::find()->orderBy(['key' => SORT_ASC])->all()
));

$special = implode(', ', array_map(
    function (Special2 $model): string {
        return $model->key;
    },
    Special2::find()->orderBy(['key' => SORT_ASC])->all()
));

$type = implode(', ', array_map(
    function (WeaponType2 $type): string {
        return $type->key;
    },
    WeaponType2::find()->orderBy(['key' => SORT_ASC])->all()
));
?>
<?= $this->renderFile(__DIR__ . '/migration.php', [
    'className' => $className,
    'namespace' => $namespace,
    'inTransaction' => true,
    'traits' => [
        'app\components\db\WeaponMigration',
    ],
    'upCode' => implode("\n", [
        "foreach (\$this->getWeapons() as \$weaponData) {",
        "    call_user_func_array([\$this, 'upWeapon'], \$weaponData);",
        "}",
    ]),
    'downCode' => implode("\n", [
        "foreach (\$this->getWeapons() as \$weaponData) {",
        "    \$this->downWeapon(\$weaponData[0]);",
        "}",
    ]),
    'extraCode' => implode("\n", [
        "public function getWeapons(): array",
        "{",
        "    // type: {$type}",
        "    // sub: {$sub}",
        "    // special: {$special}",
        "    return [",
        "        ['key', 'Name', 'type', 'sub', 'special', 'main_ref', 'reskin_of', 42],",
        "        ['key', 'Name', 'type', 'sub', 'special', 'main_ref', 'reskin_of', 42],",
        "        ['key', 'Name', 'type', 'sub', 'special', 'main_ref', 'reskin_of', 42],",
        "        ['key', 'Name', 'type', 'sub', 'special', 'main_ref', 'reskin_of', 42],",
        "    ];",
        "}",
    ]),
]) ?>
