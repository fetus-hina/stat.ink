<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\commands\license\LicenseCheckTrait;
use app\commands\license\LicenseExtractTrait;
use yii\console\Controller;

class LicenseController extends Controller
{
    use LicenseCheckTrait;
    use LicenseExtractTrait;

    public $defaultAction = 'check';
}
