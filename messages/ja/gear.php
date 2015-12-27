<?php
return array_merge(
    [
        'Headgear'  => 'アタマ',
        'Clothing'  => 'フク',
        'Shoes'     => 'クツ',
    ],
    require(__DIR__ . '/gear-headgear.php'),
    require(__DIR__ . '/gear-clothing.php'),
    require(__DIR__ . '/gear-shoes.php')
);
