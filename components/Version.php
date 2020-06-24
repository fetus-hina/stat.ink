<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Yii;
use app\components\helpers\GitHelper;

class Version
{
    private static ?string $revision = null;
    private static ?string $shortRevision = null;
    private static ?DateTimeImmutable $lastCommited = null;

    public static function getVersion(): ?string
    {
        $v = trim((string)Yii::$app->version);
        return ($v !== '') ? $v : null;
    }

    public static function getRevision(): ?string
    {
        static::fetchRevision();
        return static::$revision ?: null;
    }

    public static function getShortRevision(): ?string
    {
        static::fetchRevision();
        return static::$shortRevision ?: null;
    }

    public static function getLastCommited(): ?DateTimeImmutable
    {
        static::fetchRevision();
        return static::$lastCommited ?: null;
    }

    public static function getFullHash(string $shortHash): ?string
    {
        return GitHelper::getLine(['rev-parse', $shortHash, '-q']);
    }

    public static function getVersionTags(?string $hash = null): array
    {
        $revision = $hash ?? static::getRevision();

        if (!preg_match('/^[0-9a-f]+$/', $revision)) {
            return [];
        }

        if (!$lines = GitHelper::get(['tag', '--points-at', $revision])) {
            return [];
        }

        $lines = array_filter($lines, fn ($_) => (bool)preg_match('/^v?\d+\.\d+\.\d+/', $_));
        usort($lines, fn ($a, $b) => version_compare($b, $a));

        return $lines;
    }

    private static function fetchRevision(): void
    {
        if (
            static::$revision !== null &&
            static::$shortRevision !== null &&
            static::$lastCommited !== null
        ) {
            return;
        }

        try {
            if (!$line = static::getGitLog('%H:%h:%cd')) {
                throw new Exception();
            }

            $revisions = explode(':', $line);
            if (count($revisions) !== 3) {
                throw new Exception();
            }

            static::$revision = $revisions[0];
            static::$shortRevision = $revisions[1];
            static::$lastCommited = (new DateTimeImmutable())
                ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
                ->setTimestamp((int)$revisions[2]);
        } catch (Exception $e) {
            static::$revision = null;
            static::$shortRevision = null;
            static::$lastCommited = null;
        }
    }

    private static function getGitLog($format): ?string
    {
        return GitHelper::getLine(['log', '-n', 1, '--format' => $format, '--date' => 'raw']);
    }
}
