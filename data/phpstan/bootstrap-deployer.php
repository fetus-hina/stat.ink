<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

// Deployer adds its package root to include_path at runtime, so that
// deploy/deploy.php can `require 'recipe/yii.php'`. Do the same for PHPStan.
set_include_path(
    dirname(__DIR__, 2) . '/deploy/vendor/deployer/deployer' . PATH_SEPARATOR . get_include_path(),
);
