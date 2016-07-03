<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

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
