<?php
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
