<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Collator;
use Exception;
use Yii;
use yii\console\ExitCode;
use yii\helpers\StringHelper;
use yii\i18n\Formatter;

use function array_key_exists;
use function array_reduce;
use function escapeshellarg;
use function exec;
use function explode;
use function max;
use function min;
use function str_starts_with;
use function strlen;
use function substr;
use function time;
use function trim;
use function uksort;
use function vsprintf;

use const PHP_INT_MAX;

final class GitAuthorHelper
{
    public static function getCopyrightYear(string $path): string
    {
        $authors = self::getAuthors($path);
        $minCommitDate = array_reduce(
            $authors,
            fn (int $carry, array $item): int => min($carry, $item[0]),
            time(),
        );

        $f = Yii::createObject([
            'class' => Formatter::class,
            'timeZone' => 'Asia/Tokyo',
        ]);

        if ($f->asDate($minCommitDate, 'yyyy') === $f->asDate(time(), 'yyyy')) {
            return $f->asDate($minCommitDate, 'yyyy');
        }

        return vsprintf('%s-%s', [
            $f->asDate($minCommitDate, 'yyyy'),
            $f->asDate(time(), 'yyyy'),
        ]);
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function getAuthors(string $path): array
    {
        $cmdline = vsprintf('/usr/bin/env git log --pretty=%s -- %s', [
            escapeshellarg('%at/%an <%ae>%n%ct/%cn <%ce>'),
            escapeshellarg($path),
        ]);
        $status = null;
        $lines = [];
        @exec($cmdline, $lines, $status);
        if ($status !== ExitCode::OK) {
            throw new Exception('Could not get contributors');
        }

        $results = [
            'AIZAWA Hina <hina@fetus.jp>' => [time(), time()],
        ];

        $appBasePath = TypeHelper::string(Yii::getAlias('@app/'));
        if (str_starts_with($path, $appBasePath)) {
            $relPath = substr($path, strlen($appBasePath));
            foreach (self::getCopyLeftAuthors($relPath) as $author) {
                $results[$author] = [time(), time()];
            }
        }

        foreach ($lines as $line) {
            if (!$line = trim($line)) {
                continue;
            }

            [$timestamp, $author] = explode('/', $line, 2);
            if (!$author = self::fixAuthor($author)) {
                continue;
            }

            $results[$author] ??= [PHP_INT_MAX, 0];
            $results[$author][0] = min($results[$author][0], (int)$timestamp);
            $results[$author][1] = max($results[$author][1], (int)$timestamp);
        }

        $locale = TypeHelper::instanceOf(Collator::create('en_US'), Collator::class);
        $locale->setAttribute(Collator::NUMERIC_COLLATION, Collator::ON);

        uksort(
            $results,
            function (string $a, string $b) use ($locale): int {
                if ($a === $b) {
                    return 0;
                }

                if (str_starts_with($a, 'AIZAWA Hina')) {
                    return -1;
                }

                if (str_starts_with($b, 'AIZAWA Hina')) {
                    return 1;
                }

                return $locale->compare($a, $b);
            },
        );

        return $results;
    }

    /**
     * @return string[]
     */
    private static function getCopyLeftAuthors(string $relPath): array
    {
        static $data = null;
        if ($data === null) {
            $data = (require __DIR__ . '/../../config/author-map.php')['copyLeft'];
        }

        $results = [];
        foreach ($data as $author => $patterns) {
            foreach ($patterns as $pattern) {
                if (StringHelper::matchWildcard($pattern, $relPath, ['filePath' => true])) {
                    $results[] = $author;
                    break;
                }
            }
        }

        return $results;
    }

    private static function fixAuthor(string $author): ?string
    {
        static $authorMap = null;
        if ($authorMap === null) {
            $authorMap = (require __DIR__ . '/../../config/author-map.php')['map'];
        }

        if (!array_key_exists($author, $authorMap)) {
            return $author;
        }

        return $authorMap[$author];
    }
}
