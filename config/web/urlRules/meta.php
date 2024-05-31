<?php

declare(strict_types=1);

return [
    '<action:[\w-]+>' => 'site/<action>',
    '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
    'robots.txt' => 'site/robots',
    '' => 'site/index',
];
