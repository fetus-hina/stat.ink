<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use app\components\helpers\battleSummarizer\Splatoon1;
use app\components\helpers\battleSummarizer\Splatoon2;
use app\components\helpers\battleSummarizer\Splatoon3;

final class BattleSummarizer
{
    use Splatoon1;
    use Splatoon2;
    use Splatoon3;
}
