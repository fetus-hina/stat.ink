<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

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
        $php[] = '// This config file is updated by `yii asset/up-revision`.';
        $php[] = '// DO NOT EDIT';
        $php[] = '';
        $php[] = 'declare(strict_types=1);';
        $php[] = '';
        $php[] = sprintf('return %d;', $version);
        $php[] = '';

        file_put_contents($path, implode("\n", $php));
        fprintf(STDERR, "Asset revision is updated to %d.\n", $version);

        return 0;
    }
}
