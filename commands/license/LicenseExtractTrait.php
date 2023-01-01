<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\license;

use DirectoryIterator;
use Yii;
use stdClass;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;

use function array_shift;
use function copy;
use function escapeshellarg;
use function file_exists;
use function file_get_contents;
use function fwrite;
use function pathinfo;
use function preg_match;
use function preg_replace;
use function strcasecmp;
use function strcmp;
use function strnatcasecmp;
use function trim;
use function usort;
use function vsprintf;

trait LicenseExtractTrait
{
    use Helper;

    public function actionExtract(): int
    {
        $packages = $this->getPackages();
        $this->extractPackages($packages);
        return 0;
    }

    private function getPackages(): array
    {
        $cmdline = vsprintf('/usr/bin/env %s --no-interaction --no-plugins license --format=%s', [
            escapeshellarg(Yii::getAlias('@app/composer.phar')),
            escapeshellarg('json'),
        ]);
        return ArrayHelper::getValue(
            Json::decode($this->execCommand($cmdline)),
            'dependencies',
        );
    }

    private function extractPackages(array $packages): void
    {
        foreach ($packages as $name => $info) {
            $this->extractPackage(
                isset($info['version']) && trim((string)$info['version']) !== ''
                    ? "{$name}@{$info['version']}"
                    : $name,
                Yii::getAlias('@app/vendor') . '/' . $name,
            );
        }
    }

    private function extractPackage(string $packageName, string $baseDir): bool
    {
        if (!file_exists($baseDir)) {
            fwrite(STDERR, "license/extract: Directory does not exists: $packageName\n");
            return false;
        }

        if (!$path = $this->findLicense($packageName, $baseDir)) {
            fwrite(STDERR, "license/extract: license file does not exists: $baseDir\n");
            return false;
        }

        $distPath = implode('/', [
            Yii::getAlias('@app/data/licenses-composer'),
            $this->sanitize($packageName) . '-LICENSE.txt',
        ]);
        if (!FileHelper::createDirectory(dirname($distPath))) {
            fwrite(
                STDERR,
                'license/extract: could not create directory: ' . dirname($distPath) . "\n",
            );
            return false;
        }
        copy($path, $distPath);
        return true;
    }

    private function findLicense(string $name, string $dir): ?string
    {
        $precedence = [
            '/^LICEN[CS]E$/i',
            '/^LICEN[CS]E\-\w+$/i', // e.g. LICENSE-MIT
            '/^MIT-LICEN[CS]E$/i',
            '/^COPYING$/i',
            '/^README$/i',
        ];

        $files = [];
        $it = new DirectoryIterator($dir);
        foreach ($it as $entry) {
            if ($entry->isDot() || $entry->isDir()) {
                continue;
            }

            $path = $entry->getPathname();
            $basename = $entry->getBasename();
            $filename = pathinfo($basename, PATHINFO_FILENAME);

            foreach ($precedence as $i => $regexp) {
                if (preg_match($regexp, $filename)) {
                    $files[] = (object)[
                        'precedence' => $i,
                        'basename' => $basename,
                        'path' => $path,
                    ];
                }
            }
        }

        if (!$files) {
            fwrite(STDERR, "license/extract: no license file detected on {$name}\n");
            return null;
        }

        usort($files, fn (stdClass $a, stdClass $b): int => $a->precedence <=> $b->precedence
                ?: strnatcasecmp($a->basename, $b->basename)
                ?: strcasecmp($a->basename, $b->basename)
                ?: strcmp($a->basename, $b->basename));

        while ($files) {
            $info = array_shift($files);
            if ($this->hasLicense($info->path)) {
                return $info->path;
            }
        }
        return null;
    }

    private function hasLicense(string $path): bool
    {
        $text = file_get_contents($path, false);
        return (bool)preg_match('/license|copyright/i', $text);
    }

    private function sanitize(string $packageName): string
    {
        $packageName = preg_replace(
            '/[^!#$%()+,.\/-9@-Z_a-z]+/',
            '-',
            $packageName,
        );
        $packageName = str_replace('/../', '/', $packageName);
        $packageName = str_replace('/./', '/', $packageName);
        return $packageName;
    }
}
