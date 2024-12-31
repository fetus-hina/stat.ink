<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

/**
 * @var string $className the new migration class name without namespace
 * @var string $namespace the new migration class namespace
 */

use app\components\db\VersionMigration;
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
        VersionMigration::class,
    ],
    'upCode' => implode("\n", [
        '$this->upVersion3(',
        '    \'NEW.VER\',',
        '    \'vNEW.VER.x\',',
        '    \'NEW.VER.SION\',',
        '    \'vNEW.VER.SION\',',
        '    new DateTimeImmutable(\'YYYY-MM-DDT10:10:00+09:00\'),',
        ');',
        '',
        'return true;',
    ]),
    'downCode' => implode("\n", [
        '$this->downVersion3(\'NEW.VER.SION\', \'' . addslashes($latestVersion->tag) . '\');',
        '',
        'return true;',
    ]),
    'extraCode' => implode("\n", [
        '/**',
        ' * @inheritdoc',
        ' */',
        'protected function vacuumTables(): array',
        '{',
        '    return [',
        '        \'{{%splatoon_version3}}\',',
        '        \'{{%splatoon_version_group3}}\',',
        '    ];',
        '}',
    ]),
]) ?>
