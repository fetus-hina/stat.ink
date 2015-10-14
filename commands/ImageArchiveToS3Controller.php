<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Credentials\CredentialProvider;
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
                $tmpFile = new Resource(tempnam('s3up-', sys_get_temp_dir()), 'unlink');
                if ($this->convertToWebP($entry->getPathname(), $tmpFile->get()) &&
                        $this->upload($tmpFile->get(), $match[1] . '.webp'))
                {
                    unlink($entry->getPathname());
                }
            }
        }
    }

    protected function convertToWebP($png, $webp)
    {
        $this->stdOut(sprintf("%s: converting to webp\n", basename($png)));
        $cmdline = sprintf(
            '/usr/bin/env %s -lossless -o %s %s >/dev/null 2>&1',
            escapeshellarg('cwebp'),
            escapeshellarg($webp),
            escapeshellarg($png)
        );
        $lines = $status = null;
        @exec($cmdline, $lines, $status);
        return $status == 0;
    }

    protected function upload($filePath, $key)
    {
        $this->stdOut(sprintf("Uploading as %s\n", $key));
        try {
            $client = $this->s3Client;
            $ret = $client->upload(
                Yii::$app->params['amazonS3']['bucket'],
                $key,
                file_get_contents($filePath, false, null),
                'public-read',
                [
                    'params' => [
                        'StorageClass' => 'REDUCED_REDUNDANCY',
                    ]
                ]
            );
            if ($ret->hasKey('ObjectURL') && $ret->get('ObjectURL') != '') {
                return true;
            }
        } catch (\Exception $e) {
        }
        return false;
    }

    protected function getIsConfigured()
    {
        $params = Yii::$app->params['amazonS3'];
        return $params['accessKey'] && $params['secret'] && $params['bucket'];
    }

    protected function getS3Client()
    {
        return new S3Client([
            'credentials' => $this->awsCredentialProvider,
            'version' => '2006-03-01',
            'region' => Yii::$app->params['amazonS3']['region'],
        ]);
    }

    protected function getAwsCredentialProvider()
    {
        return CredentialProvider::fromCredentials(
            $this->awsCredentials
        );
    }

    protected function getAwsCredentials()
    {
        $params = Yii::$app->params['amazonS3'];
        return new Credentials($params['accessKey'], $params['secret']);
    }
}
