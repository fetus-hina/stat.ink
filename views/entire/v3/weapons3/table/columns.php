<?php

declare(strict_types=1);

return [
  require __DIR__ . '/columns/weapon.php',
  require __DIR__ . '/columns/subweapon.php',
  require __DIR__ . '/columns/special.php',

  require __DIR__ . '/columns/battles.php',
  require __DIR__ . '/columns/battles-bar.php',
  require __DIR__ . '/columns/winpct.php',

  require __DIR__ . '/columns/avg-kill.php',
  require __DIR__ . '/columns/avg-death.php',
  require __DIR__ . '/columns/kill-ratio.php',
  require __DIR__ . '/columns/kill-per-min.php',
  require __DIR__ . '/columns/death-per-min.php',
  require __DIR__ . '/columns/avg-assist.php',
  require __DIR__ . '/columns/assist-per-min.php',
  require __DIR__ . '/columns/avg-ka.php',
  require __DIR__ . '/columns/ka-per-min.php',
  require __DIR__ . '/columns/ka-ratio.php',
  require __DIR__ . '/columns/avg-special.php',
  require __DIR__ . '/columns/special-per-min.php',
  require __DIR__ . '/columns/avg-inked.php',
  require __DIR__ . '/columns/inked-per-min.php',
];
