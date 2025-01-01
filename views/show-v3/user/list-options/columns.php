<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
  require __DIR__ . '/columns/buttons.php',
  require __DIR__ . '/columns/lobby-icon.php',
  require __DIR__ . '/columns/lobby.php',
  require __DIR__ . '/columns/rule-icon.php',
  require __DIR__ . '/columns/rule.php',
  require __DIR__ . '/columns/stage.php',
  require __DIR__ . '/columns/weapon-icon.php',
  require __DIR__ . '/columns/weapon.php',
  require __DIR__ . '/columns/subweapon-icon.php',
  require __DIR__ . '/columns/subweapon.php',
  require __DIR__ . '/columns/special-icon.php',
  require __DIR__ . '/columns/special.php',
  require __DIR__ . '/columns/rank-before.php',
  require __DIR__ . '/columns/rank-after.php',
  require __DIR__ . '/columns/fest-power.php',
  require __DIR__ . '/columns/level-before.php',
  require __DIR__ . '/columns/level-after.php',
  require __DIR__ . '/columns/judge.php',
  require __DIR__ . '/columns/result.php',
  require __DIR__ . '/columns/kill-death.php',
  // k/min
  // d/min
  require __DIR__ . '/columns/kill-ratio.php',
  require __DIR__ . '/columns/kill-rate.php',
  require __DIR__ . '/columns/kill-assist.php',
  require __DIR__ . '/columns/specials.php',
  // sp/min
  require __DIR__ . '/columns/inked.php',
  // inked/min
  require __DIR__ . '/columns/medals.php',
  require __DIR__ . '/columns/rank-in-team.php',
  require __DIR__ . '/columns/elapsed-mmss.php',
  require __DIR__ . '/columns/elapsed-sec.php',
  require __DIR__ . '/columns/datetime.php',
  require __DIR__ . '/columns/timezone.php',
  require __DIR__ . '/columns/reltime.php',
];
