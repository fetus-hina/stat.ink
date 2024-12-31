<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
  require __DIR__ . '/columns/players.php',
  require __DIR__ . '/columns/use-rate.php',
  require __DIR__ . '/columns/win-rate.php',
  require __DIR__ . '/columns/kill.php',
  require __DIR__ . '/columns/death.php',
  require __DIR__ . '/columns/kill-ratio.php',
  require __DIR__ . '/columns/assist.php',
  require __DIR__ . '/columns/special.php',
  require __DIR__ . '/columns/inked.php',
];
