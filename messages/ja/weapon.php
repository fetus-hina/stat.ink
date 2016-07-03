<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

return array_merge(
    [
        'All of {0}' => 'すべての{0}',
        'Any Weapon' => 'すべてのブキ',
        'Chargers' => 'チャージャー',
        'Rollers' => 'ローラー',
        'Shooters' => 'シューター',
        'Sloshers' => 'スロッシャー',
        'Splatlings' => 'スピナー',
    ],
    require(__DIR__ . '/weapon-shooter.php'),
    require(__DIR__ . '/weapon-roller.php'),
    require(__DIR__ . '/weapon-charger.php'),
    require(__DIR__ . '/weapon-slosher.php'),
    require(__DIR__ . '/weapon-splatling.php')
);
