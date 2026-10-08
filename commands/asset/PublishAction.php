<?php

/**
 * @copyright Copyright (C) 2021-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\commands\asset;

use Uri\WhatWg\Url as WhatWgUrl;
use yii\base\Action;
use yii\helpers\Url;

use function escapeshellarg;
use function fwrite;
use function implode;
use function passthru;
use function sprintf;

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
        $parsedUrl = WhatWgUrl::parse($url);
        if (
            $parsedUrl &&
            ($parsedUrl->getScheme() === 'http' || $parsedUrl->getScheme() === 'https')
        ) {
            return [
                $parsedUrl->getAsciiHost(),
                $parsedUrl->getPort() ?? ($parsedUrl->getScheme() === 'http' ? 80 : 443),
            ];
        }
        return [null, null];
    }
}
