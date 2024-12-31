<?php

/**
 * @copyright Copyright (C) 2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return array_merge(
  [
    require __DIR__ . '/columns/weapon.php',
    require __DIR__ . '/columns/icon-subweapon.php',
    require __DIR__ . '/columns/icon-special.php',
  ],
  require __DIR__ . '/columns-common.php',
);
