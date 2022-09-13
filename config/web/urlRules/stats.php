<?php

declare(strict_types=1);

return [
    'fest/<region:\w+>/<order:\d+>' => 'fest/view',
    'entire/weapons/<weapon:\w+>/<rule:\w+>' => 'entire/weapon',
    'entire/weapons/<weapon:\w+>' => 'entire/weapon',
    'entire/weapons2/<weapon:\w+>/<rule:\w+>' => 'entire/weapon2',
    'entire/weapons2-tier/v<version:[0-9.]+>@<month:[0-9]{4}-[0-9]{2}>/<rule:[\w]+>' => 'entire/weapons2-tier',
    'entire/users/combined-<b32name:[A-Za-z2-7]+>' => 'entire/combined-agent',
    'entire/users/<b32name:[A-Za-z2-7]+>' => 'entire/agent',
    'stages/<year:\d+>/<month:\d+>' => 'stage/month',
    'stages/<map:[a-z]+>/<rule:[a-z0-9_]+>.json' => 'stage/map-history-json',
    'stages/<map:[a-z]+>/<rule:[a-z0-9_]+>' => 'stage/map-detail',
    'stages/<map:[a-z]+>' => 'stage/map',
    'stages' => 'stage/index',
    'downloads' => 'download-stats/index',
    'entire/salmon/<action:[\w-]+>' => 'entire/salmon-<action>',
];
