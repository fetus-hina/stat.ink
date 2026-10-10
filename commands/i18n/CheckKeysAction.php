<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\commands\i18n;

use FilesystemIterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Yii;
use app\components\i18n\IcuMessageChecker;
use app\components\i18n\TranslationCallExtractor;
use app\components\i18n\TranslationKeyChecker;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\console\ExitCode;
use yii\i18n\PhpMessageSource;

use function array_map;
use function array_unique;
use function basename;
use function count;
use function dirname;
use function file_exists;
use function file_get_contents;
use function fprintf;
use function glob;
use function in_array;
use function is_array;
use function is_string;
use function str_contains;
use function str_replace;
use function str_starts_with;
use function strlen;
use function substr;
use function usort;

use const STDERR;
use const STDOUT;

/**
 * Checks the translation catalogs
 *
 * 1. Every `Yii::t('app*', '...')` message exists in the Japanese catalog.
 *    The Japanese catalogs are the master: `./yii i18n/messages` copies their
 *    keys to the other languages. A missing key does not raise any error at
 *    runtime; Yii just shows the source (English) text, so it is easily
 *    overlooked.
 * 2. Every translation is a valid ICU message and keeps the placeholders of its
 *    source message. Machine translations (`messages/_deepl`) are not checked.
 */
final class CheckKeysAction extends Action
{
    private const MASTER_LANGUAGE = 'ja';

    private const EXCLUDE_DIRS = [
        '.git',
        'messages',
        'node_modules',
        'runtime',
        'tests',
        'vendor',
        'web',
    ];

    /**
     * Translations that intentionally drop a placeholder
     */
    private const ICU_CHECK_EXCEPTIONS = [
        // Splatoon 1 Splatfest titles; the French titles do not include the team name
        'fr/fest.php' => [
            '{0} Champion',
            '{0} Defender',
            '{0} Fanboy',
            '{0} Fiend',
            '{0} King',
            '{1} Champion',
            '{1} Defender',
            '{1} Fangirl',
            '{1} Fiend',
            '{1} Queen',
        ],
    ];

    public function run(): int
    {
        $root = (string)Yii::getAlias('@app');

        $calls = [];
        $dynamicCount = 0;
        foreach ($this->findPhpFiles($root) as $path) {
            $result = TranslationCallExtractor::extract((string)file_get_contents($path));
            $relPath = substr($path, strlen($root) + 1);
            foreach ($result['calls'] as $call) {
                $calls[] = $call + ['file' => $relPath];
            }
            $dynamicCount += count($result['dynamic']);
        }

        $categories = array_unique(array_map(fn (array $c): string => $c['category'], $calls));
        $missing = TranslationKeyChecker::findMissing($calls, $this->loadCatalogs($categories));
        usort(
            $missing,
            fn (array $a, array $b): int => [$a['file'], $a['line']] <=> [$b['file'], $b['line']],
        );

        foreach ($missing as $m) {
            fprintf(
                STDOUT,
                "%s:%d: [%s] %s (%s)\n",
                $m['file'],
                $m['line'],
                $m['category'],
                $m['message'],
                $m['reason'],
            );
        }

        fprintf(
            STDERR,
            "Checked %d calls, %d missing. %d calls with non-literal arguments were skipped.\n",
            count($calls),
            count($missing),
            $dynamicCount,
        );

        $icuIssueCount = $this->checkIcuMessages($root);

        return $missing || $icuIssueCount > 0 ? ExitCode::DATAERR : ExitCode::OK;
    }

    private function checkIcuMessages(string $root): int
    {
        $messageCount = 0;
        $issueCount = 0;
        foreach ((array)glob($root . '/messages/*/*.php') as $path) {
            $language = basename(dirname((string)$path));
            if (str_starts_with($language, '_')) {
                continue;
            }

            $catalog = require $path;
            if (!is_array($catalog)) {
                continue;
            }

            $relPath = substr((string)$path, strlen($root) + 1);
            $exceptions = self::ICU_CHECK_EXCEPTIONS[$language . '/' . basename((string)$path)] ?? [];
            $locale = str_replace('-', '_', $language);
            foreach ($catalog as $source => $translation) {
                $source = (string)$source;
                if (
                    !is_string($translation) ||
                    $translation === '' ||
                    (!str_contains($source, '{') && !str_contains($translation, '{')) ||
                    in_array($source, $exceptions, true)
                ) {
                    continue;
                }

                ++$messageCount;
                foreach (IcuMessageChecker::check($source, $translation, $locale) as $issue) {
                    ++$issueCount;
                    fprintf(
                        STDOUT,
                        "%s: [%s] %s => %s (%s)\n",
                        $relPath,
                        $issue['type'],
                        $source,
                        $translation,
                        $issue['detail'],
                    );
                }
            }
        }

        fprintf(
            STDERR,
            "Checked %d translations with placeholders, %d issues.\n",
            $messageCount,
            $issueCount,
        );

        return $issueCount;
    }

    /**
     * @return iterable<string>
     */
    private function findPhpFiles(string $root): iterable
    {
        $it = new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
                fn (SplFileInfo $file, string $path, RecursiveDirectoryIterator $it): bool => $it->hasChildren()
                    ? !($it->getSubPath() === '' && in_array($file->getFilename(), self::EXCLUDE_DIRS, true))
                    : $file->getExtension() === 'php',
            ),
        );

        foreach ($it as $file) {
            yield $file->getPathname();
        }
    }

    /**
     * @param string[] $categories
     * @return array<string, array<string, string>|null>
     */
    private function loadCatalogs(array $categories): array
    {
        $catalogs = [];
        foreach ($categories as $category) {
            $path = $this->getCatalogPath($category);
            if ($path === null) {
                continue; // not managed by our PHP catalogs (e.g., "yii", "db*")
            }

            $catalog = file_exists($path) ? require $path : null;
            $catalogs[$category] = is_array($catalog) ? $catalog : null;
        }
        return $catalogs;
    }

    private function getCatalogPath(string $category): ?string
    {
        try {
            $source = Yii::$app->i18n->getMessageSource($category);
        } catch (InvalidConfigException) {
            return null;
        }

        // Only our own catalogs (e.g., "yii" is translated by the framework)
        $basePath = (string)Yii::getAlias($source instanceof PhpMessageSource ? $source->basePath : '');
        if ($basePath !== (string)Yii::getAlias('@app/messages')) {
            return null;
        }

        $file = $source->fileMap[$category] ?? str_replace('\\', '/', $category) . '.php';
        return $basePath . '/' . self::MASTER_LANGUAGE . '/' . $file;
    }
}
