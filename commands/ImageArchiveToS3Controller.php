<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use S3;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\components\helpers\Resource;

class ImageArchiveToS3Controller extends Controller
{
    public $defaultAction = 'upload';

    public function actionUpload()
    {
        if (!$this->isConfigured) {
            $this->stdError("stat.ink for Amazon S3 is not configured.\n");
            return 1;
        }
        if (!$lock = $this->getLock()) {
            return 0;
        }
        $dirPath = Yii::getAlias('@app/runtime/image-archive-tmp');
        if (!file_exists($dirPath)) {
            return 0;
        }
        $it = new \DirectoryIterator($dirPath);
        foreach ($it as $entry) {
            if ($entry->isDot() || !$entry->isFile()) {
                continue;
            }
            if (preg_match('/^(\d+-\w+)\.png$/', $entry->getBasename(), $match)) {
                $tmpPng  = new Resource(tempnam('s3up-', sys_get_temp_dir()), 'unlink');
                $tmpWebP = new Resource(tempnam('s3up-', sys_get_temp_dir()), 'unlink');
                if ($this->convertToOptimizedPNG($entry->getPathname(), $tmpPng->get()) &&
                        $this->convertToWebP($entry->getPathname(), $tmpWebP->get())
                ) {
                    // ok. let's upload.
                    $conf = [
                        'png' => [
                            'localPath' => $tmpPng->get(),
                            'uploadKey' => $match[1] . '.png',
                        ],
                        'webp' => [
                            'localPath' => $tmpWebP->get(),
                            'uploadKey' => $match[1] . '.webp',
                        ],
                    ];
                    if ($this->upload($conf)) {
                        unlink($entry->getPathname());
                    }
                }
            }
        }
    }

    protected function convertToOptimizedPNG($in, $out)
    {
        $this->stdOut(sprintf("%s: Optimizing\n", basename($in)), Console::FG_YELLOW);
        $cmdline = sprintf(
            '/usr/bin/env %s -rem allb -l 9 %s %s >/dev/null 2>&1',
            escapeshellarg('pngcrush'),
            escapeshellarg($in),
            escapeshellarg($out)
        );
        $lines = $status = null;
        @exec($cmdline, $lines, $status);
        if ($status != 0) {
            return false;
        }
        $this->stdOut(sprintf(
                "  Optimized. %s => %s\n", 
                number_format(filesize($in)),
                number_format(filesize($out))
            ),
            Console::FG_GREEN
        );
        return true;
    }

    protected function convertToWebP($png, $webp)
    {
        $this->stdOut(sprintf("%s: Converting to WebP\n", basename($png)), Console::FG_YELLOW);
        $cmdline = sprintf(
            '/usr/bin/env %s -lossless -o %s %s >/dev/null 2>&1',
            escapeshellarg('cwebp'),
            escapeshellarg($webp),
            escapeshellarg($png)
        );
        $lines = $status = null;
        @exec($cmdline, $lines, $status);
        if ($status != 0) {
            return false;
        }
        $this->stdOut(sprintf(
                "  Converted. %s bytes.\n",
                number_format(filesize($webp))
            ),
            Console::FG_GREEN
        );
        return true;
    }

    protected function upload($config)
    {
        foreach (Yii::$app->params['amazonS3'] as $param) {
            if (!$param['accessKey'] || !$param['secret'] || !$param['bucket']) {
                return false;
            }
            $type = @$param['type'] ?: 'webp';
            $endpoint = @$param['endpoint'] ?: 's3.amazonaws.com';

            if (!$this->doUpload(
                    @$param['name'],
                    $param['accessKey'],
                    $param['secret'],
                    $endpoint,
                    $config[$type]['localPath'],
                    $param['bucket'],
                    $config[$type]['uploadKey']
                )
            ) {
                return false;
            }
        }
        return true;
    }

    protected function doUpload($name, $accessKey, $secret, $endpoint, $localPath, $bucket, $key)
    {
        $this->stdOut(sprintf("[%s] Uploading as %s\n", $name, $key), Console::FG_YELLOW);
        try {
            S3::setExceptions();
            if (!$file = S3::inputFile($localPath)) {
                return false;
            }
            S3::setAuth($accessKey, $secret);
            S3::setSSL(true, strpos($endpoint, 'amazonaws') !== false);
            S3::setEndpoint($endpoint);
            $ret = S3::putObject(
                $file,
                $bucket,
                $key,
                S3::ACL_PRIVATE,
                [],
                [],
                S3::STORAGE_CLASS_RRS,
                S3::SSE_NONE
            );
            if ($ret) {
                $this->stdOut("  Upload successful.\n", Console::FG_GREEN);
                return true;
            }
        } catch (\Exception $e) {
            $this->stdOut("  Caught Exception: " . $e->getMessage() . "\n", Console::FG_RED);
            var_dump($e);
        }
        $this->stdOut("  Upload failed.\n", Console::FG_RED);
        return false;
    }

    protected function getIsConfigured()
    {
        $params = Yii::$app->params['amazonS3'];
        foreach ($params as $param) {
            if ($param['accessKey'] && $param['secret'] && $param['bucket']) {
                return true;
            }
        }
        return false;
    }

    protected function getLock()
    {
        $lockPath = Yii::getAlias('@app/runtime/.s3lock');
        if (!$fh = fopen($lockPath, 'c')) {
            return false;
        }
        if (!flock($fh, LOCK_EX | LOCK_NB)) {
            fclose($fh);
            return false;
        }
        return new Resource(
            $fh,
            function ($fh) {
                flock($fh, LOCK_UN);
                fclose($fh);
            }
        );
    }
}
