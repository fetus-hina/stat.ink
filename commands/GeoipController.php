<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Curl\Curl;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

class GeoipController extends Controller
{
    protected const BASE_DIR = '@app/data/GeoIP';
    protected const TGZ_BASE_URL = 'https://geolite.maxmind.com/download/geoip/database/';

    protected $saveFiles = [
        'GeoLite2-City.tar.gz' => [
            'COPYRIGHT.txt',
            'GeoLite2-City.mmdb',
            'LICENSE.txt',
        ],
        'GeoLite2-Country.tar.gz' => [
            'GeoLite2-Country.mmdb',
        ],
    ];

    // data/GeoIP/%.mmdb: data/GeoIP/%.tar.gz
    //     @mkdir -p $(dir $@)
    //     tar -zxf $< --strip=1 --no-same-owner -C data/GeoIP */$(notdir $@)
    //     @touch $@

    //     data/GeoIP/%.txt: data/GeoIP/GeoLite2-Country.tar.gz
    //     @mkdir -p $(dir $@)
    //     tar -zxf $< --strip=1 --no-same-owner -C data/GeoIP */$(notdir $@)
    //     @touch $@

    //     data/GeoIP/%.tar.gz:
    //     @mkdir -p $(dir $@)
    //     curl -fsSL -o $@ https://geolite.maxmind.com/download/geoip/database/$(notdir $@)
    //     @touch $@

    //     .PRECIOUS: data/GeoIP/%.tar.gz

    public $defaultAction = 'update';

    public function actionUpdate(): int
    {
        return $this->updateFiles() ? 0 : 1;
    }

    protected function updateFiles(): bool
    {
        $success = true;
        foreach ($this->saveFiles as $tgzFileName => $innerFiles) {
            $tgzFilePath = rtrim(Yii::getAlias(static::BASE_DIR), '/') . '/' . $tgzFileName;
            $tgzURL = rtrim(Yii::getAlias(static::TGZ_BASE_URL), '/') . '/' . $tgzFileName;

            if (!$this->downloadFile($tgzURL, $tgzFilePath)) {
                $success = false;
                continue;
            }

            if (!$this->extractFiles($tgzFilePath, $innerFiles)) {
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

            fprintf(STDERR, "Downloading %s from %s\n", basename($savePath), $url);

            $curl = new Curl();
            $curl->get($url);
            if ($curl->error) {
                fwrite(STDERR, "Could not download from {$url}\n");
                return false;
            }

            file_put_contents($savePath, $curl->rawResponse);
            fwrite(STDERR, "Downloaded the file.\n");
            if (filesize($savePath) < $threshold) {
                vfprintf(STDERR, "It's too small! (actual) %d < (needed) %d\n", [
                    filesize($savePath),
                    $threshold,
                ]);
                return false;
            }

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

        $cmdline = vsprintf('/usr/bin/env %s -z -x -f %s --strip=1 --no-same-owner -C %s %s', [
            escapeshellarg('tar'),
            escapeshellarg($archivePath),
            escapeshellarg(rtrim(Yii::getAlias(static::BASE_DIR), '/')),
            implode(' ', array_map(
                function (string $fileName): string {
                    return escapeshellarg('*/' . $fileName);
                },
                $files
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
}
