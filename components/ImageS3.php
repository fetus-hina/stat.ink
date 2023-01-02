<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components;

use S3;
use yii\base\Component;

use function base64_encode;
use function hash;
use function strlen;
use function strpos;

class ImageS3 extends Component
{
    public $enabled = false;
    public $endpoint = 's3-ap-northeast-1.amazonaws.com';
    public $accessKey;
    public $secret;
    public $bucket;

    public $acl; // S3::ACL_PUBLIC_READ
    public $storageClass; // S3::STORAGE_CLASS_STANDARD

    public function init()
    {
        parent::init();
        if (!$this->acl) {
            $this->acl = S3::ACL_PUBLIC_READ;
        }
        if (!$this->storageClass) {
            $this->storageClass = S3::STORAGE_CLASS_STANDARD;
        }
    }

    public function uploadFile(string $path, string $serverPath): bool
    {
        if (!$this->enabled) {
            return false;
        }
        if (!$file = S3::inputFile($path, true)) {
            return false;
        }
        return $this->doUpload($file, $serverPath);
    }

    public function upload(string $data, string $serverPath): bool
    {
        if (!$this->enabled) {
            return false;
        }
        $file = [
            'data' => $data,
            'size' => strlen($data),
            'md5sum' => base64_encode(hash('md5', $data, true)),
        ];
        return $this->doUpload($file, $serverPath);
    }

    private function doUpload(array $file, string $serverPath): bool
    {
        S3::setEndpoint($this->endpoint);
        S3::setAuth($this->accessKey, $this->secret);
        S3::setSSL(true, strpos($this->endpoint, 'amazonaws') !== false);
        S3::setExceptions(true);
        return (bool)S3::putObject(
            $file,
            $this->bucket,
            $serverPath,
            $this->acl,
            [],
            [],
            $this->storageClass,
        );
    }
}
