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
    const MAX_PROC_COUNT = 3;

    public $defaultAction = 'upload';

    public function actionUpload()
    {
        if (!$this->isConfigured) {
            $this->stderr("stat.ink for Amazon S3 is not configured.\n", Consolle::FG_RED);
            return 1;
        }
        if (!$lock = $this->getLock()) {
            $this->stdout("Another process is running.\n");
            return 0;
        }
        $dirPath = Yii::getAlias('@app/runtime/image-archive-tmp');
        if (!file_exists($dirPath)) {
            return 0;
        }

        $queue = $this->findQueuedFiles($dirPath);
        $this->stdout("Queue size: " . count($queue) . "\n");

        $procs = [];
        $error = false;
        while (!$error && !empty($queue)) {
            // 規定のプロセス数になるまでプロセスを作る
            while (!$error &&
                    !empty($queue) &&
                    count($procs) < self::MAX_PROC_COUNT
            ) {
                $path = array_shift($queue);
                $cmdline = sprintf(
                    '/usr/bin/env %s %s %s',
                    escapeshellarg(dirname(__DIR__) . '/yii'),
                    escapeshellarg('image-archive-to-s3/upload-internal'),
                    escapeshellarg($path)
                );
                $descSpec = [
                    ['pipe', 'r'],
                    ['pipe', 'w'],
                ];
                $pipes = null;
                if (!$proc = @proc_open($cmdline, $descSpec, $pipes)) {
                    $this->stderr("ERROR: Unable to create child process for $path\n", Console::FG_RED);
                    $error = true;
                    continue;
                }
                $this->stdout("Created child process for $path\n");
                $res = new Resource($proc, 'proc_close');
                fclose($pipes[0]);
                $procs[] = $res;
            }

            // 死んだプロセスがいれば後片付け
            $terminated = 0;
            foreach ($procs as $i => $proc) {
                $status = proc_get_status($proc->get());
                if (!$status['running']) {
                    if ($status['exitcode'] !== 0) {
                        $error = true;
                    }
                    unset($procs[$i]);
                    ++$terminated;
                }
            }

            // プロセスリストの詰め直し
            if ($terminated > 0) {
                $this->stdout("{$terminated} child process(es) terminated.\n");
                $procs = array_values($procs);
            }

            usleep(200 * 1000);
        }

        // キューは空になったがまだ子はいるかもしれないので終了待ち
        while (!empty($procs)) {
            $terminated = 0;
            foreach ($procs as $i => $proc) {
                $status = proc_get_status($proc->get());
                if (!$status['running']) {
                    if ($status['exitcode'] !== 0) {
                        $error = true;
                    }
                    unset($procs[$i]);
                    ++$terminated;
                }
            }

            // プロセスリストの詰め直し
            if ($terminated > 0) {
                $this->stdout("{$terminated} child process(es) terminated.\n");
                $procs = array_values($procs);
            }
        }

        return $error ? 1 : 0;
    }

    public function actionUploadInternal($filePath)
    {
        if (!$this->isConfigured) {
            $this->stderr("stat.ink for Amazon S3 is not configured.\n");
            return 1;
        }
        if (!file_exists($filePath)) {
            $this->stderr("upload-internal: No such file: $filePath\n", Console::FG_RED);
            return 1;
        }
        if (!is_file($filePath)) {
            $this->stderr("upload-internal: object is not a file: $filePath\n", Console::FG_RED);
            return 1;
        }
        if (!is_readable($filePath)) {
            $this->stderr("upload-internal: file is not readable: $filePath\n", Console::FG_RED);
            return 1;
        }
        if (!preg_match('!/(\d+-\w+)\.png!', $filePath, $match)) {
            $this->stderr("upload-internal: invalid filename format: $filePath\n", Console::FG_RED);
            return 1;
        }

        $tmpPng  = new Resource(tempnam('s3up-', sys_get_temp_dir()), 'unlink');
        if (!$this->convertToOptimizedPNG($filePath, $tmpPng->get())) {
            $this->stderr("upload-internal: unable to optimize file: $filePath\n", Console::FG_RED);
            return 1;
        }

        $conf = [
            'png' => [
                'localPath' => $tmpPng->get(),
                'uploadKey' => $match[1] . '.png',
            ],
        ];
        if (!$this->upload($conf)) {
            return 1;
        }
        unlink($filePath);
        return 0;
    }

    protected function convertToOptimizedPNG($in, $out)
    {
        $this->stdout(sprintf("%s: Optimizing\n", basename($in)), Console::FG_YELLOW);
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
        $this->stdout(
            sprintf(
                "  Optimized. %s => %s\n",
                number_format(filesize($in)),
                number_format(filesize($out))
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
            $type = 'png';
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
        $this->stdout(sprintf("[%s] Uploading as %s\n", $name, $key), Console::FG_YELLOW);
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
                $this->stdout("  Upload successful.\n", Console::FG_GREEN);
                return true;
            }
        } catch (\Exception $e) {
            $this->stderr("  Caught Exception: " . $e->getMessage() . "\n", Console::FG_RED);
        }
        $this->stdout("  Upload failed.\n", Console::FG_RED);
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

    protected function findQueuedFiles($dirPath)
    {
        $list = [];
        $it = new \DirectoryIterator($dirPath);
        foreach ($it as $entry) {
            if ($entry->isDot() || !$entry->isFile()) {
                continue;
            }
            $list[] = $entry->getPathname();
            if (count($list) >= 1000) {
                break;
            }
        }
        natcasesort($list);
        return array_values($list);
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
