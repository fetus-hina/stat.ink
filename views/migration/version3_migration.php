<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */

use app\models\SplatoonVersion3;

$latestVersion = SplatoonVersion3::find()
    ->orderBy(['id' => SORT_DESC])
    ->limit(1)
    ->one();
?>
<?= $this->renderFile(__DIR__ . '/migration.php', [
    'className' => $className,
    'namespace' => $namespace,
    'inTransaction' => true,
    'traits' => [
        'app\components\db\VersionMigration',
    ],
    'upCode' => implode("\n", [
        '$this->upVersion3(',
        '    \'NEW.VER\',',
        '    \'vNEW.VER.x\',',
        '    \'NEW.VER.SION\',',
        '    \'vNEW.VER.SION\',',
        '    new DateTimeImmutable(\'YYYY-MM-DDT10:00:00+09:00\')',
        ');',
    ]),
    'downCode' => implode("\n", [
        '$this->downVersion3(\'NEW.VER.SION\', \'' . addslashes($latestVersion->tag) . '\');',
    ]),
]) ?>
