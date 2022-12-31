<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\asset;

use Yii;
use yii\base\Action;

use function file_exists;
use function file_put_contents;
use function fprintf;
use function implode;
use function sprintf;

class UpRevisionAction extends Action
{
    /**
     * Update revision number of assets
     *
     * You should be update the number on deploy action.
     * The number will be used in public asset path.
     *
     * @return int
     */
    public function run()
    {
        $version = 0;
        $path = Yii::getAlias('@app/config/asset-revision.php');
        if (file_exists($path)) {
            $version = (int)require $path;
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
        $php[] = 'return (function (): int {';
        $php[] = sprintf('    return %d;', $version);
        $php[] = '})();';

        file_put_contents($path, implode("\n", $php) . "\n");
        fprintf(STDERR, "Asset revision is updated to %d.\n", $version);

        return 0;
    }
}
