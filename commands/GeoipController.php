<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Curl\Curl;
use Exception;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

use function array_map;
use function basename;
use function dirname;
use function escapeshellarg;
use function exec;
use function file_exists;
use function filesize;
use function fprintf;
use function fwrite;
use function http_build_query;
use function implode;
use function preg_replace_callback;
use function rtrim;
use function str_repeat;
use function strlen;
use function unlink;
use function vfprintf;
use function vsprintf;

use const STDERR;

class GeoipController extends Controller
{
    protected const BASE_DIR = '@app/data/GeoIP';

    public $defaultAction = 'update';

    // https://dev.maxmind.com/geoip/geoipupdate/#Direct_Downloads
    protected $saveFiles = null;

    public function init()
    {
        parent::init();

        if (!$this->saveFiles) {
            $this->saveFiles = [
                'GeoLite2-City.tar.gz' => [
                    'url' => vsprintf('%s?%s', [
                        'https://download.maxmind.com/app/geoip_download',
                        http_build_query([
                            'edition_id' => 'GeoLite2-City',
                            'license_key' => '[GEOIP_LICENSE_KEY]',
                            'suffix' => 'tar.gz',
                        ]),
                    ]),
                    'files' => [
                        'COPYRIGHT.txt',
                        'GeoLite2-City.mmdb',
                        'LICENSE.txt',
                    ],
                ],
                'GeoLite2-Country.tar.gz' => [
                    'url' => vsprintf('%s?%s', [
                        'https://download.maxmind.com/app/geoip_download',
                        http_build_query([
                            'edition_id' => 'GeoLite2-Country',
                            'license_key' => '[GEOIP_LICENSE_KEY]',
                            'suffix' => 'tar.gz',
                        ]),
                    ]),
                    'files' => [
                        'GeoLite2-Country.mmdb',
                    ],
                ],
            ];
        }
    }

    public function actionUpdate(): int
    {
        return $this->updateFiles() ? 0 : 1;
    }

    protected function updateFiles(): bool
    {
        $success = true;
        foreach ($this->saveFiles as $tgzFileName => $info) {
            $tgzFilePath = rtrim(Yii::getAlias(static::BASE_DIR), '/') . '/' . $tgzFileName;
            if (!$this->downloadFile($this->rewriteEnvValue($info['url']), $tgzFilePath)) {
                $success = false;
                continue;
            }

            if (!$this->extractFiles($tgzFilePath, $info['files'])) {
                $success = false;
                continue;
            }
        }
        return $success;
    }

    protected function downloadFile(string $url, string $savePath, int $threshold = 1): bool
    {
        try {
            $saveDir = dirname($savePath);
            if (!@file_exists($saveDir)) {
                fwrite(STDERR, "Creating directory {$saveDir}\n");
                if (!FileHelper::createDirectory($saveDir, 0755, true)) {
                    fwrite(STDERR, "Failed to create the directory.\n");
                    return false;
                }
            }

            if (file_exists($savePath)) {
                vfprintf(STDERR, "Removing old file %s\n", [
                    basename($savePath),
                ]);

                if (!@unlink($savePath)) {
                    fwrite(STDERR, "Failed to remove the old file.\n");
                    return false;
                }
            }

            vfprintf(STDERR, "Downloading %s from %s\n", [
                basename($savePath),
                preg_replace_callback(
                    '/\b(license_key=)([^&]+)/',
                    fn (array $match): string => $match[1] . str_repeat('*', strlen($match[2])),
                    $url,
                ),
            ]);

            $curl = new Curl();
            if (!$curl->download($url, $savePath)) {
                fwrite(STDERR, "Could not download from {$url}\n");
                return false;
            }

            fwrite(STDERR, "Downloaded the file.\n");
            if (filesize($savePath) < $threshold) {
                vfprintf(STDERR, "It's too small! (actual) %d < (needed) %d\n", [
                    filesize($savePath),
                    $threshold,
                ]);
                return false;
            }

            vfprintf(STDERR, "File size is %d\n", [
                filesize($savePath),
            ]);

            return true;
        } catch (Throwable $e) {
            fprintf(STDERR, "Catch an Exception! (%s)\n", $e->getMessage());
            return false;
        }
    }

    protected function extractFiles(string $archivePath, array $files): bool
    {
        vfprintf(STDERR, "Extracting files from %s (files: %s)\n", [
            basename($archivePath),
            implode(', ', $files),
        ]);

        $cmdline = vsprintf('/usr/bin/env %s -zxf %s --strip=1 --no-same-owner --wildcards -C %s %s', [
            escapeshellarg('tar'),
            escapeshellarg($archivePath),
            escapeshellarg(rtrim(Yii::getAlias(static::BASE_DIR), '/')),
            implode(' ', array_map(
                fn (string $fileName): string => escapeshellarg('*/' . $fileName),
                $files,
            )),
        ]);
        @exec($cmdline, $line, $status);
        if ($status !== 0) {
            fwrite(STDERR, "Extract error. cmdline={$cmdline}\n");
            return false;
        }

        fwrite(STDERR, "Extracted.\n");
        return true;
    }

    protected function rewriteEnvValue(string $text): string
    {
        return preg_replace_callback(
            '/(?:\[|%5[bB])([0-9A-Z_]+)(?:\]|%5[dD])/',
            function (array $match): string {
                $envName = $match[1];
                if (!isset($_SERVER[$envName])) {
                    throw new Exception(vsprintf('The environment variable `%s` is not defined.', [
                        $envName,
                    ]));
                }

                return $_SERVER[$envName];
            },
            $text,
        );
    }
}
