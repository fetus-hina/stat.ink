<?php

/**
 * @copyright Copyright (C) 2021-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\asset;

use DateTimeImmutable;
use DateTimeZone;
use FilesystemIterator;
use SplFileInfo;
use Yii;
use app\components\helpers\TypeHelper;
use yii\base\Action;
use yii\console\ExitCode;
use yii\helpers\FileHelper;

use function array_filter;
use function array_keys;
use function array_slice;
use function count;
use function file_exists;
use function fprintf;
use function fwrite;
use function is_dir;
use function is_readable;
use function sprintf;
use function strlen;
use function substr;
use function uasort;

use const STDERR;

final class CleanupAction extends Action
{
    private const ASSET_PRESERVE_REVISIONS = 2; // 2 revisions
    private const ASSET_PRESERVE_SECONDS = 3600; // 1 hour

    /**
     * @return int
     */
    public function run()
    {
        $baseDir = Yii::getAlias('@app/web/assets');
        if (!file_exists($baseDir) || !is_readable($baseDir) || !is_dir($baseDir)) {
            fwrite(STDERR, "Assets directory {$baseDir} is not iteratable.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        fwrite(STDERR, "Finding cleanup targets...\n");
        $directories = $this->getCleanupTargets(
            $this->findDirectories($baseDir),
        );
        if (!$directories) {
            fwrite(STDERR, "No cleanup targets.\n");
            return ExitCode::OK;
        }

        fprintf(STDERR, "Found %d cleanup targets.\n", count($directories));
        foreach (array_keys($directories) as $directory) {
            fprintf(STDERR, "  %s\n", $directory);
            $this->cleanUp($directory);
        }

        return ExitCode::OK;
    }

    /**
     * @return array<string, DateTimeImmutable>
     */
    private function findDirectories(string $baseDir): array
    {
        $results = [];
        $it = new FilesystemIterator(
            $baseDir,
            FilesystemIterator::CURRENT_AS_FILEINFO |
                FilesystemIterator::KEY_AS_PATHNAME |
                FilesystemIterator::SKIP_DOTS |
                FilesystemIterator::UNIX_PATHS,
        );

        foreach ($it as $entry) {
            $entry = TypeHelper::instanceOf($entry, SplFileInfo::class);
            if (
                $entry->isDir() &&
                substr($entry->getBasename(), 0, 1) !== '.'
            ) {
                $results[$entry->getPathname()] = (new DateTimeImmutable())
                    ->setTimezone(new DateTimeZOne('Etc/UTC'))
                    ->setTimestamp(TypeHelper::int($entry->getMTime()));
            }
        }
        unset($it, $entry);

        uasort(
            $results,
            fn (DateTimeImmutable $a, DateTimeImmutable $b): int => $b <=> $a,
        );

        return $results;
    }

    /**
     * @param array<string, DateTimeImmutable> $directories
     */
    private function getCleanupTargets(array $directories): array
    {
        $threshold = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp($_SERVER['REQUEST_TIME'])
            ->modify(sprintf('-%d second', self::ASSET_PRESERVE_SECONDS));

        $directories = array_slice(
            $directories,
            self::ASSET_PRESERVE_REVISIONS,
            preserve_keys: true,
        );

        return array_filter(
            $directories,
            fn (DateTimeImmutable $time): bool => $time < $threshold,
        );
    }

    private function cleanUp(string $directory): void
    {
        $baseDir = (string)Yii::getAlias('@app/web/assets') . '/';

        // safety check
        if (substr($directory, 0, strlen($baseDir)) !== $baseDir) {
            fwrite(STDERR, "Invalid directory: {$directory}\n");
            return;
        }

        FileHelper::removeDirectory($directory);
    }
}
