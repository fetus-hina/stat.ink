<?php

use app\components\db\VersionMigration;
use app\models\SplatoonVersion2;
use yii\base\View;

/**
 * @var View $this
 * @var string $className string the new migration class name without namespace
 * @var string $namespace string the new migration class namespace
 */

$latestVersion = SplatoonVersion2::find()
    ->orderBy(['id' => SORT_DESC])
    ->limit(1)
    ->one();
?>
<?= $this->renderFile(__DIR__ . '/migration.php', [
    'className' => $className,
    'namespace' => $namespace,
    'inTransaction' => true,
    'traits' => [
        VersionMigration::class,
    ],
    'upCode' => implode("\n", [
        '$this->upVersion2(',
        '    \'NEW.VER\',',
        '    \'NEW.VER.x\',',
        '    \'NEW.VER.SION\',',
        '    \'NEW.VER.SION\',',
        '    new DateTimeImmutable(\'YYYY-MM-DDT11:00:00+09:00\')',
        ');',
    ]),
    'downCode' => implode("\n", [
        '$this->downVersion2(\'NEW.VER.SION\', \'' . addslashes($latestVersion->tag) . '\');',
    ]),
]) ?>
