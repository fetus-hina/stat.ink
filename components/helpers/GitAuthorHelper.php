<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use Exception;
use Yii;
use yii\console\ExitCode;
use yii\i18n\Formatter;

use function escapeshellarg;
use function exec;
use function min;
use function time;
use function trim;
use function vsprintf;

final class GitAuthorHelper
{
    public static function getCopyrightYear(string $path): string
    {
        $minCommitDate = self::getEarliestCommitTimestamp($path);

        $f = Yii::createObject([
            'class' => Formatter::class,
            'timeZone' => 'Asia/Tokyo',
        ]);

        $startYear = $f->asDate($minCommitDate, 'yyyy');
        $currentYear = $f->asDate(time(), 'yyyy');

        return $startYear === $currentYear
            ? $startYear
            : vsprintf('%s-%s', [$startYear, $currentYear]);
    }

    private static function getEarliestCommitTimestamp(string $path): int
    {
        $cmdline = vsprintf('/usr/bin/env git log --pretty=%s -- %s', [
            escapeshellarg('%at%n%ct'),
            escapeshellarg($path),
        ]);
        $status = null;
        $lines = [];
        @exec($cmdline, $lines, $status);
        if ($status !== ExitCode::OK) {
            throw new Exception('Could not get commits');
        }

        $earliest = time();
        foreach ($lines as $line) {
            if (!$line = trim($line)) {
                continue;
            }
            $earliest = min($earliest, (int)$line);
        }
        return $earliest;
    }
}
