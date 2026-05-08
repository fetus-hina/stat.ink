<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\commands;

use DirectoryIterator;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Yii;
use app\components\helpers\GitHelper;
use app\components\helpers\TypeHelper;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

use function array_map;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function implode;
use function in_array;
use function ltrim;
use function preg_quote;
use function preg_replace_callback;
use function rtrim;
use function str_starts_with;
use function substr;
use function vsprintf;

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
        $this->rewritePhpRecursive(dirname(__DIR__), '');
        $this->rewritePhpRecursive(dirname(__DIR__) . '/messages/ja', 'messages/ja');

        return ExitCode::OK;
    }

    public function actionRewriteModels(): int
    {
        $this->rewritePhpRecursive(dirname(__DIR__) . '/models', 'models');

        return ExitCode::OK;
    }

    private function rewritePhpRecursive(string $dir, string $relPath): void
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
                            'node_modules',
                            'resources',
                            'runtime',
                            'tests',
                            'utils.internal',
                            'vendor',
                            'web',
                        ],
                        true,
                    )
                ) {
                    continue;
                }

                $this->rewritePhpRecursive(
                    $entry->getPathname(),
                    ltrim($relPath . '/' . $entry->getFilename(), '/'),
                );
            } elseif ($entry->isFile() && substr($entry->getFilename(), -4) === '.php') {
                $fileRelPath = ltrim($relPath . '/' . $entry->getFilename(), '/');
                if ($fileRelPath === 'requirements.php') {
                    continue;
                }

                $this->rewritePhpFile($entry->getPathname(), $fileRelPath);
            }
        }
    }

    private function rewritePhpFile(string $path, string $relPath): void
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
            echo Console::ansiFormat($relPath, [Console::FG_GREY]) . "\n";
            return;
        }

        echo Console::ansiFormat($relPath, [Console::FG_PURPLE]) . "\n";
        $differ = new Differ(new UnifiedDiffOutputBuilder());
        echo $differ->diff($file, $newFile) . "\n";

        file_put_contents($path, rtrim($newFile) . "\n");
    }

    private function makeDocComment(string $path): ?string
    {
        $lines = [
            vsprintf('@copyright Copyright (C) %s AIZAWA Hina', [
                GitHelper::getCopyrightYear($path),
            ]),
            '@license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT',
        ];

        return "/**\n" .
            implode("\n", array_map(fn (string $line): string => " * {$line}", $lines)) .
            "\n */";
    }
}
