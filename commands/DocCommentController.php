<?php

/**
 * @copyright Copyright (C) 2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Collator;
use DirectoryIterator;
use Exception;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Yii;
use app\components\helpers\TypeHelper;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

use function array_key_exists;
use function array_keys;
use function array_map;
use function array_reduce;
use function dirname;
use function escapeshellarg;
use function exec;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function implode;
use function in_array;
use function max;
use function min;
use function preg_quote;
use function preg_replace_callback;
use function rtrim;
use function str_starts_with;
use function substr;
use function time;
use function trim;
use function uksort;
use function vsprintf;

use const PHP_INT_MAX;

final class DocCommentController extends Controller
{
    public $defaultAction = 'rewrite-php';

    public function init()
    {
        parent::init();

        Yii::$app->timeZone = 'Asia/Tokyo';
    }

    public function actionRewritePhp(): int
    {
        $this->rewritePhpRecursive(dirname(__DIR__));
        $this->rewritePhpRecursive(dirname(__DIR__) . '/messages/ja');

        return ExitCode::OK;
    }

    private function rewritePhpRecursive(string $dir): void
    {
        $it = new DirectoryIterator($dir);
        foreach ($it as $entry) {
            if ($entry->isDot()) {
                continue;
            }

            if ($entry->isDir()) {
                if (
                    $dir === dirname(__DIR__) &&
                    in_array(
                        $entry->getFilename(),
                        [
                            'bin',
                            'data',
                            'deploy',
                            'doc',
                            'docker',
                            'messages',
                            'migrations',
                            'models', // 一旦 models は除外
                            'node_modules',
                            'resources',
                            'runtime',
                            'tests',
                            'utils.internal',
                            'vendor',
                            'views', // 一旦 views は除外
                            'web',
                        ],
                        true,
                    )
                ) {
                    continue;
                }

                $this->rewritePhpRecursive($entry->getPathname());
            } elseif ($entry->isFile() && substr($entry->getFilename(), -4) === '.php') {
                if ($entry->getFilename() === 'requirements.php') {
                    continue;
                }

                $this->rewritePhpFile($entry->getPathname());
            }
        }
    }

    private function rewritePhpFile(string $path): void
    {
        $file = TypeHelper::string(file_get_contents($path));
        if (!str_starts_with($file, '<?php')) {
            echo "Skip: {$path}\n";
            return;
        }

        $docComment = $this->makeDocComment($path);

        $regexPhpOpenTag = preg_quote('<?php', '/') . '[\x20-\x7e]*\n+';
        $regexDocComment = preg_quote('/**', '/') . '.*?' . preg_quote('*/', '/') . '\n*';
        $regex = "/^({$regexPhpOpenTag})(?:{$regexDocComment})?/s";

        $newFile = preg_replace_callback(
            $regex,
            fn (array $match): string => rtrim($match[1]) . "\n\n{$docComment}\n\n",
            $file,
            1,
        );
        if ($file === $newFile) {
            echo Console::ansiFormat($path, [Console::FG_GREY]) . "\n";
            return;
        }

        echo Console::ansiFormat($path, [Console::FG_PURPLE]) . "\n";
        $differ = new Differ(new UnifiedDiffOutputBuilder());
        print $differ->diff($file, $newFile) . "\n";

        file_put_contents($path, rtrim($newFile) . "\n");
    }

    private function makeDocComment(string $path): ?string
    {
        $f = Yii::$app->formatter;

        $authors = $this->getAuthors($path);

        $minCommitDate = array_reduce(
            $authors,
            fn (int $carry, array $item): int => min($carry, $item[0]),
            time(),
        );

        $lines = [];
        $lines[] = vsprintf('@copyright Copyright (C) %s AIZAWA Hina', [
            $f->asDate($minCommitDate, 'yyyy') === $f->asDate(time(), 'yyyy')
                ? $f->asDate($minCommitDate, 'yyyy')
                : vsprintf('%s-%s', [
                    $f->asDate($minCommitDate, 'yyyy'),
                    $f->asDate(time(), 'yyyy'),
                ]),
        ]);
        $lines[] = '@license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT';
        foreach (array_keys($authors) as $author) {
            $lines[] = vsprintf('@author %s', [
                $author,
            ]);
        }

        return "/**\n" .
            implode("\n", array_map(fn (string $line): string => " * {$line}", $lines)) .
            "\n */";
    }

    /**
     * @return array<string, array{int, int}>
     */
    private function getAuthors(string $path): array
    {
        $cmdline = vsprintf('/usr/bin/env git log --pretty=%s -- %s | sort | uniq', [
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
        foreach ($lines as $line) {
            if (!$line = trim($line)) {
                continue;
            }

            [$timestamp, $author] = explode('/', $line, 2);
            if (!$author = $this->fixAuthor($author)) {
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

    private function fixAuthor(string $author): ?string
    {
        $authorMap = [
            'AIZAWA Hina <hina@bouhime.com>' => 'AIZAWA Hina <hina@fetus.jp>',
            'AIZAWA, Hina <hina@bouhime.com>' => 'AIZAWA Hina <hina@fetus.jp>',
            'GitHub <noreply@github.com>' => null,
            'Lukas <github@muffl0n.de>' => 'Lukas Böttcher <github@muffl0n.de>',
            'StyleCI Bot <bot@styleci.io>' => null,
            'Unknown <wkoichi@gmail.com>' => 'Koichi Watanabe <wkoichi@gmail.com>',
            'Yifan <44556003+liuyifan-eric@users.noreply.github.com>' => 'Yifan Liu <yifanliu00@gmail.com>',
            'spacemeowx2 <spacemeowx2@gmail.com>' => 'imspace <spacemeowx2@gmail.com>',
        ];

        return array_key_exists($author, $authorMap) ? $authorMap[$author] : $author;
    }
}
