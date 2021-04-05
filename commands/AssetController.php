<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Yii;
use app\commands\asset\CleanupAction;
use yii\console\Controller;
use yii\helpers\Url;

use function escapeshellarg;
use function file_exists;
use function file_put_contents;
use function fprintf;
use function implode;
use function is_array;
use function parse_url;
use function passthru;
use function sprintf;
use function strtolower;

class AssetController extends Controller
{
    /** @return array */
    public function actions()
    {
        return [
            'cleanup' => CleanupAction::class,
        ];
    }

    public function actionPublish(): int
    {
        $url = Url::to(['site/asset-publish'], true);
        list($host, $port) = $this->getHostAndPortFromURL($url);
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

    public function actionUpRevision(): int
    {
        $version = 0;
        $path = Yii::getAlias('@app/config/asset-revision.php');
        if (file_exists($path)) {
            $version = (int)require($path);
        }
        ++$version;
        $php = [];
        $php[] = '<?php';
        $php[] = '';
        $php[] = 'declare(strict_types=1);';
        $php[] = '';
        $php[] = '// This config file is updated by `yii asset/up-revision`.';
        $php[] = '// DO NOT EDIT';
        $php[] = '';
        $php[] = sprintf('return %d;', $version);
        $php[] = '';

        file_put_contents($path, implode("\n", $php));
        fprintf(STDERR, "Asset revision is updated to %d.\n", $version);

        return 0;
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
                (isset($urlInfo['port']))
                    ? (int)$urlInfo['port']
                    : ($urlInfo['scheme'] === 'http' ? 80 : 443),
            ];
        }
        return [null, null];
    }
}
