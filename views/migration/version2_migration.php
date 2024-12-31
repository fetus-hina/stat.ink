<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */

use app\models\SplatoonVersion2;

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
        'app\components\db\VersionMigration',
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
