<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use Zend\Uri\Http as HttpUri;
use yii\console\Controller;
use yii\helpers\Url;

class AssetController extends Controller
{
    public function actionPublish(): int
    {
        $url = Url::to(['site/asset-publish'], true);
        $host = null;
        $port = null;
        try {
            $uri = new HttpUri($url);
            $host = $uri->getHost();
            $port = $uri->getPort();
        } catch (\Exception $e) {
        }
        if (!$host || !$port) {
            fwrite(STDERR, "Unable to detect host name/port\n");
            return 1;
        }

        $cmdline = implode(' ', [
            'curl',
            '-f',
            '--resolve',
            escapeshellarg(sprintf('%s:%d:127.0.0.1', $host, $port)),
            escapeshellarg($url),
        ]);
        echo '$ ' . $cmdline . "\n";

        passthru($cmdline, $status);
        return $status;
    }
}
