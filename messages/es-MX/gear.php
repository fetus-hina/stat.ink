<?php
return array_merge(
    [
        'Headgear'  => 'Accesorios',
        'Clothing'  => 'Ropa',
        'Shoes'     => 'Calzado',
    ],
    require(__DIR__ . '/gear-headgear.php'),
    require(__DIR__ . '/gear-clothing.php'),
    require(__DIR__ . '/gear-shoes.php')
);
