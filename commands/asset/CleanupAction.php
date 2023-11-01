<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\asset;

use CallbackFilterIterator;
use DateTimeImmutable;
use DateTimeZone;
use FilesystemIterator;
use SplFileInfo;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

use function array_reduce;
use function basename;
use function checkdate;
use function dirname;
use function file_exists;
use function fwrite;
use function is_dir;
use function is_readable;
use function preg_match;
use function rename;
use function substr;

use const STDERR;

class CleanupAction extends Action
{
    private const ASSET_REVISION_CLEANUP_THRESHOLD = 10; // 10 revisions
    private const COMMIT_TIME_CLEANUP_THRESHOLD = 120 * 86400; // 120 days
    private const MTIME_CLEANUP_THRESHOLD = 180 * 86400; // 180 days

    private ?int $currentRevision = null;
    private DateTimeImmutable $now;

    /** @return void */
    public function init()
    {
        parent::init();
        $this->currentRevision = ArrayHelper::getValue(Yii::$app->params, 'assetRevision');
        $this->now = new DateTimeImmutable('now', new DateTimeZone('Etc/UTC'));
    }

    /** @return int */
    public function run()
    {
        $baseDir = Yii::getAlias('@app/web/assets');
        if (!file_exists($baseDir) || !is_readable($baseDir) || !is_dir($baseDir)) {
            fwrite(STDERR, "Assets directory {$baseDir} is not iteratable.\n");
            return 1;
        }

        $it = new CallbackFilterIterator(
            new FilesystemIterator(
                $baseDir,
                array_reduce(
                    [
                        FilesystemIterator::CURRENT_AS_FILEINFO,
                        FilesystemIterator::KEY_AS_PATHNAME,
                        FilesystemIterator::SKIP_DOTS,
                        FilesystemIterator::UNIX_PATHS,
                    ],
                    fn (int $carry, int $cur) => ($carry | $cur),
                    0, // init value
                ),
            ),
            fn (SplFileInfo $f) => $f->isDir()
        );
        foreach ($it as $path => $entry) {
            $baseName = basename($path);
            if (substr($baseName, 0, 7) === 'DELETE-') {
                $this->procDirectory($entry, null, null);
            } elseif (
                preg_match(
                    '/^([0-9]{4})([0-9]{2})([0-9]{2})-([0-9]{2})([0-9]{2})([0-9]{2})$/', // Ymd-His
                    $baseName,
                    $match,
                ) &&
                (2021 <= (int)$match[1] && (int)$match[1] < 2100) && // year
                (1 <= (int)$match[2] && (int)$match[2] <= 12) && // month
                (1 <= (int)$match[3] && (int)$match[3] <= 31) && // day
                checkdate((int)$match[2], (int)$match[3], (int)$match[1]) &&
                (0 <= (int)$match[4] && (int)$match[4] < 24) && // hour
                (0 <= (int)$match[5] && (int)$match[5] < 60) && // minute
                (0 <= (int)$match[6] && (int)$match[6] <= 60) //second (may leap sec)
            ) {
                // Ymd-His format
                $this->procDirectory(
                    $entry,
                    (new DateTimeImmutable('@0', new DateTimeZone('Etc/UTC')))
                        ->setDate((int)$match[1], (int)$match[2], (int)$match[3])
                        ->setTime((int)$match[4], (int)$match[5], (int)$match[6]),
                    null,
                );
            } elseif (
                preg_match(
                    '/^([0-9]{4})([0-9]{2})([0-9]{2})-([0-9]+)$/', // Ymd-nnn format (n=seq)
                    $baseName,
                    $match,
                ) &&
                (2021 <= (int)$match[1] && (int)$match[1] < 2100) && // year
                (1 <= (int)$match[2] && (int)$match[2] <= 12) && // month
                (1 <= (int)$match[3] && (int)$match[3] <= 31) && // day
                checkdate((int)$match[2], (int)$match[3], (int)$match[1])
            ) {
                // Ymd-nnn format
                $this->procDirectory(
                    $entry,
                    (new DateTimeImmutable('@0', new DateTimeZone('Etc/UTC')))
                        ->setDate((int)$match[1], (int)$match[2], (int)$match[3])
                        ->setTime(23, 59, 59),
                    (int)$match[4],
                );
            } elseif (preg_match('/^[a-z2-7]{16}$/', $baseName)) {
                $this->procDirectory($entry, null, null);
            } else {
                fwrite(STDERR, "Unknown format: {$baseName}\n");
            }
        }

        return 42;
    }

    private function procDirectory(
        SplFileInfo $dir,
        ?DateTimeImmutable $commitTime,
        ?int $assetRevision,
    ): void {
        if (!$this->shouldBeDeleted($dir, $commitTime, $assetRevision)) {
            return;
        }

        fwrite(STDERR, "Delete: {$dir->getPathname()}\n");

        // atomic にするため、一旦名前を変更する
        if (substr(basename($dir->getPathname()), 0, 7) !== 'DELETE-') {
            $tmpDirName = dirname($dir->getPathname()) . '/DELETE-' . basename($dir->getPathname());
            rename($dir->getPathname(), $tmpDirName);
        } else {
            $tmpDirName = $dir->getPathname();
        }

        FileHelper::removeDirectory($tmpDirName);
    }

    private function shouldBeDeleted(
        SplFileInfo $dir,
        ?DateTimeImmutable $commitTime,
        ?int $assetRevision,
    ): bool {
        // 一時的に変更されたはずの名前が見つかった
        if (substr($dir->getBasename(), 0, 7) === 'DELETE-') {
            return true;
        }

        // リビジョン差が一定よりあるなら消す
        if ($assetRevision !== null && $this->currentRevision !== null) {
            $diff = $this->currentRevision - $assetRevision;
            return $diff > static::ASSET_REVISION_CLEANUP_THRESHOLD;
        }

        // コミット日情報が一定より古いなら消す
        if ($commitTime !== null) {
            $diff = $this->now->getTimestamp() - $commitTime->getTimestamp();
            return $diff > static::COMMIT_TIME_CLEANUP_THRESHOLD;
        }

        // mtime が一定より古いなら消す
        $diff = $this->now->getTimestamp() - $dir->getMTime();
        return $diff > static::MTIME_CLEANUP_THRESHOLD;
    }
}
