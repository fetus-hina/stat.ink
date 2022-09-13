<?php

declare(strict_types=1);

return [
    '.well-known/host-meta' => 'ostatus/host-meta',
    '.well-known/webfinger' => 'ostatus/webfinger',

    '<action:[\w-]+>'  => 'site/<action>',
    '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
    'robots.txt' => 'site/robots',
    '' => 'site/index',
];
