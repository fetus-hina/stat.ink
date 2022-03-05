<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\asset;

use yii\base\Action;
use yii\helpers\Url;

use function escapeshellarg;
use function fwrite;
use function implode;
use function is_array;
use function parse_url;
use function sprintf;
use function strtolower;

use const STDERR;

class PublishAction extends Action
{
    /** @return int */
    public function run()
    {
        $url = Url::to(['site/asset-publish'], true);
        [$host, $port] = $this->getHostAndPortFromURL($url);
        if (!$host || !$port) {
            fwrite(STDERR, "Unable to detect host name/port\n");
            return 1;
        }

        $cmdline = implode(' ', [
            'curl',
            '-fsSL',
            '--insecure',
            '--resolve',
            escapeshellarg(sprintf('%s:%d:127.0.0.1', $host, $port)),
            escapeshellarg($url),
        ]);
        echo '$ ' . $cmdline . "\n";

        passthru($cmdline, $status);
        return $status;
    }

    private function getHostAndPortFromURL(string $url): array
    {
        $urlInfo = @parse_url($url);
        if (
            is_array($urlInfo) &&
            isset($urlInfo['scheme']) &&
            isset($urlInfo['host']) &&
            ($urlInfo['scheme'] === 'http' || $urlInfo['scheme'] === 'https')
        ) {
            return [
                strtolower($urlInfo['host']),
                isset($urlInfo['port'])
                    ? (int)$urlInfo['port']
                    : ($urlInfo['scheme'] === 'http' ? 80 : 443),
            ];
        }
        return [null, null];
    }
}
