<?php
return array_merge(
    [
        'All of {0}' => 'Todos los {0}',
        'Any Weapon' => 'Cualquier principal',
        'Chargers' => 'Cargatintas',
        'Rollers' => 'Rodillos',
        'Shooters' => 'Lanzatintas',
        'Sloshers' => 'Derramatics',
        'Splatlings' => 'Tintralladoras',
    ],
    require(__DIR__ . '/weapon-shooter.php'),
    require(__DIR__ . '/weapon-roller.php'),
    require(__DIR__ . '/weapon-charger.php'),
    require(__DIR__ . '/weapon-slosher.php'),
    require(__DIR__ . '/weapon-splatling.php')
);
