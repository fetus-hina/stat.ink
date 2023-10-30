<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Throwable;
use Yii;

use function array_filter;
use function array_shift;
use function count;
use function escapeshellarg;
use function exec;
use function explode;
use function file_exists;
use function is_executable;
use function preg_match;
use function sprintf;
use function trim;
use function usort;
use function version_compare;

class Version
{
    private static $revision = null;
    private static $shortRevision = null;
    private static $lastCommited = null;

    public static function getVersion()
    {
        return Yii::$app->version;
    }

    public static function getRevision(): ?string
    {
        self::fetchRevision();
        return self::$revision ?: null;
    }

    public static function getShortRevision(): ?string
    {
        self::fetchRevision();
        return self::$shortRevision ?: null;
    }

    public static function getLastCommited(): ?DateTimeImmutable
    {
        self::fetchRevision();
        return self::$lastCommited ?: null;
    }

    public static function getFullHash(string $shortHash): ?string
    {
        $lines = static::doGit(sprintf('git rev-parse %s -q', escapeshellarg($shortHash)));
        return $lines ? array_shift($lines) : null;
    }

    public static function getVersionTags(?string $hash = null): array
    {
        $revision = $hash ?? static::getRevision();

        if (!preg_match('/^[0-9a-f]+$/', $revision)) {
            return [];
        }

        if (!$lines = static::doGit(sprintf('git tag --points-at %s', escapeshellarg($revision)))) {
            return [];
        }

        $lines = array_filter($lines, fn (string $line): bool => !!preg_match('/^v?\d+\.\d+\.\d+/', $line));

        usort($lines, fn (string $a, string $b): int => version_compare($b, $a));

        return $lines;
    }

    private static function fetchRevision()
    {
        if (
            self::$revision !== null &&
                self::$shortRevision !== null &&
                self::$lastCommited !== null
        ) {
            return;
        }

        try {
            if (!$line = self::getGitLog('%H:%h:%cd')) {
                throw new Exception();
            }
            $revisions = explode(':', $line);
            if (count($revisions) !== 3) {
                throw new Exception();
            }

            self::$revision = $revisions[0];
            self::$shortRevision = $revisions[1];
            self::$lastCommited = (new DateTimeImmutable())
                ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
                ->setTimestamp((int)$revisions[2]);
        } catch (Throwable $e) {
            self::$revision = false;
            self::$shortRevision = false;
            self::$lastCommited = false;
        }
    }

    private static function getGitLog($format)
    {
        $gitCommand = sprintf(
            'git log -n 1 --format=%s --date=raw',
            escapeshellarg($format),
        );
        if (!$lines = static::doGit($gitCommand)) {
            return false;
        }
        return trim(array_shift($lines));
    }

    private static function doGit($gitCommand)
    {
        // FIXME: scl git19 があればそれを、無ければpathの通ったgitを使うひどい場当たりhack
        if (
            file_exists('/usr/bin/scl') &&
                is_executable('/usr/bin/scl') &&
                file_exists('/opt/rh/git19/enable')
        ) {
            $cmdline = sprintf(
                '/usr/bin/scl enable git19 %s',
                escapeshellarg($gitCommand),
            );
        } else {
            $cmdline = sprintf(
                '/bin/bash -c %s',
                escapeshellarg($gitCommand),
            );
        }

        $lines = $status = null;
        exec($cmdline, $lines, $status);
        if ($status !== 0) {
            return false;
        }
        return $lines;
    }
}
