<?php

declare(strict_types=1);

return [
    'api/v3/s3s/<action:[\w-]+>' => 'api-v3/s3s-<action>',
    'api/v3/<action:[\w-]+>.<format:[\w]+>' => 'api-v3/<action>',
    'api/v3/<action:[\w-]+>' => 'api-v3/<action>',
];
