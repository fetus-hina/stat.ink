<?php

declare(strict_types=1);

return array_merge(
  [
    require __DIR__ . '/columns/weapon.php',
    require __DIR__ . '/columns/icon-subweapon.php',
    require __DIR__ . '/columns/icon-special.php',
  ],
  require __DIR__ . '/columns-common.php',
);
