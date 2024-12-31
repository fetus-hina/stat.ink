<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\dlStats3;

use Yii;
use ZipArchive;

use function basename;
use function copy;
use function implode;
use function tempnam;
use function unlink;

trait ZipUtilTrait
{
    private static function createCsvZipArchive(string $baseDirectory): bool
    {
        if (!$tmpFile = tempnam('/tmp', 'zip-')) {
            return false;
        }

        try {
            $zip = new ZipArchive();
            if (!$zip->open($tmpFile, ZipArchive::CREATE)) {
                return false;
            }

            if (!$zip->addEmptyDir(basename(Yii::getAlias($baseDirectory)))) {
                return false;
            }

            if (
                !$zip->addGlob(
                    Yii::getAlias($baseDirectory) . '/*/*/*.csv',
                    0,
                    [
                        'add_path' => basename(Yii::getAlias($baseDirectory)) . '/',
                        'remove_all_path' => true,
                    ],
                )
            ) {
                return false;
            }

            if (!$zip->close()) {
                return false;
            }

            copy(
                $tmpFile,
                implode('/', [
                    Yii::getAlias($baseDirectory),
                    basename(Yii::getAlias($baseDirectory)) . '.zip',
                ]),
            );

            return true;
        } finally {
            unlink($tmpFile);
        }
    }
}
