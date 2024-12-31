<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use app\components\helpers\randomFilename\Generator;

class RandomFilename
{
    public static function generate(string $ext = '', int $level = 0): string
    {
        return Generator::generate($ext, $level);
    }
}
